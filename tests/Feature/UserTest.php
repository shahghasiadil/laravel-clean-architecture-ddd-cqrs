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

