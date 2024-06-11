<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Repositories;

use Application\User\Data\UserData;
use Domain\User\Entities\User;
use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository implements UserRepositoryContract
{
    public function save(UserData $data): string
    {
        $user = User::create($data->all());

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

    public function update(int $id, UserData $data): bool
    {
        $user = User::findOrFail($id);

        return $user->update($data->all());
    }
}
