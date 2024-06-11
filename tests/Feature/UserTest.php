<?php

use Domain\User\Entities\User;

it('creates users', function () {

    User::factory()->count(5)->create();

    $userCount = User::count();

    $this->assertEquals(5, $userCount);

});


it('retrieves all users', function () {

    User::factory()->count(5)->create();

    $response = $this->get('/api/users');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 '*' => ['id', 'name', 'email', 'status', 'created_at', 'updated_at']
             ]);

    $this->assertCount(5, $response->json());
});

it('updates a user', function () {

    $user = User::factory()->create([
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'password' => '123456789',
        'status' => 'active'
    ]);

    $updateData = [
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com',
        'password' => '123456756',
        'status' => 'suspended'
    ];

    $response = $this->put("/api/users/{$user->id}/update", $updateData);

    $response->assertStatus(200)
             ->assertJson([
                 'id' => $user->id,
                 'name' => 'Muqtadir Khan',
                 'email' => 'muqtadir.khan@gmail.com'
             ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Muqtadir Khan',
        'email' => 'muqtadir.khan@gmail.com'
    ]);
});
