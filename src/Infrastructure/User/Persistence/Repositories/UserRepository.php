<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Repositories;

use Application\User\DTOs\CreateUserDTO;
use Domain\User\Entities\User;
use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository implements UserRepositoryContract
{
    public function save(CreateUserDTO $createUserDTO): string
    {
        $user = User::create([
            'name' => $createUserDTO->name,
            'email' => $createUserDTO->email,
            'password' => $createUserDTO->password,
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

    public function update(int $id, string $name, string $email): int
    {
        return User::query()->where('id', $id)->update([
            'name' => $name,
            'email' => $email,
        ]);
    }
}
