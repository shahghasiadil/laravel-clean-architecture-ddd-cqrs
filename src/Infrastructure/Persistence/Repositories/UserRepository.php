<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Entities\User;
use Domain\Repositories\UserRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryContract
{
    public function save(string $name, string $email, string $password): string
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        return $user->email;
    }

    public function findByEmail(string $email): ?User
    {
        $user = User::where('email', $email)->first();

        return $user ?? null;
    }

    public function getAllUsers(): Collection
    {
        return User::all();
    }
}
