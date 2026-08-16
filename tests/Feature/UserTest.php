<?php

declare(strict_types=1);

use Domain\User\Enums\UserStatus;
use Infrastructure\User\Persistence\Models\UserModel;

it('creates users', function (): void {
    UserModel::factory()->count(5)->create();

    expect(UserModel::count())->toBe(5);
});

it('registers a user through the API', function (): void {
    $response = $this->postJson('/api/v1/users', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'secret123',
        'status' => UserStatus::Active->value,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'name' => 'Ada Lovelace',
                'email' => 'ada@example.com',
                'status' => UserStatus::Active->value,
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
    ]);
});

it('rejects a duplicate email with a 422 instead of a 500', function (): void {
    UserModel::factory()->create(['email' => 'taken@example.com']);

    $response = $this->postJson('/api/v1/users', [
        'name' => 'Someone Else',
        'email' => 'taken@example.com',
        'password' => 'secret123',
        'status' => UserStatus::Active->value,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('email');
});

it('retrieves a paginated list of users', function (): void {
    UserModel::factory()->count(5)->create();

    $response = $this->getJson('/api/v1/users');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['*' => ['id', 'name', 'email', 'status', 'created_at', 'updated_at']],
            'meta' => ['page', 'per_page', 'total'],
        ]);

    expect($response->json('meta.total'))->toBe(5);
});

it('caps per_page at 100', function (): void {
    $response = $this->getJson('/api/v1/users?per_page=500');

    $response->assertStatus(422)->assertJsonValidationErrors('per_page');
});

it('updates a user without requiring a password', function (): void {
    $user = UserModel::factory()->create([
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'status' => UserStatus::Active->value,
    ]);

    $response = $this->patchJson("/api/v1/users/{$user->id}", [
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'status' => UserStatus::Suspended->value,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => 'Muqtadir Khan',
                'email' => 'muqtadir.khan@gmail.com',
                'status' => UserStatus::Suspended->value,
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'status' => UserStatus::Suspended->value,
    ]);
});

it('returns the record for the id that was actually updated, even if the email changes', function (): void {
    $userA = UserModel::factory()->create(['email' => 'a@example.com']);
    UserModel::factory()->create(['email' => 'b@example.com']);

    $response = $this->patchJson("/api/v1/users/{$userA->id}", [
        'name' => $userA->name,
        'email' => 'a-renamed@example.com',
        'status' => UserStatus::Active->value,
    ]);

    $response->assertStatus(200)->assertJsonPath('data.id', $userA->id);
});

it('returns a 404 problem-details response for a missing user', function (): void {
    $response = $this->getJson('/api/v1/users/999999');

    $response->assertStatus(404);
    expect($response->headers->get('Content-Type'))->toBe('application/problem+json');
});
