<?php

declare(strict_types=1);

use Domain\User\Entities\User;
use Domain\User\Enums\UserStatus;

it('creates users', function (): void {

    User::factory()->count(5)->create();

    $userCount = User::count();

    $this->assertEquals(5, $userCount);

});


it('retrieves all users', function (): void {

    User::factory()->count(5)->create();

    $response = $this->get('/api/users');

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => ['id', 'name', 'email', 'status', 'created_at', 'updated_at'],
        ]);

    $this->assertCount(5, $response->json());
});

it('updates a user', function (): void {

    $user = User::factory()->create([
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'password' => '123456789',
        'status' => UserStatus::Active->value,
    ]);

    $updateData = [
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'password' => '123456756',
        'status' => UserStatus::Suspended->value,
    ];

    $response = $this->patch("/api/users/{$user->id}", $updateData);

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
