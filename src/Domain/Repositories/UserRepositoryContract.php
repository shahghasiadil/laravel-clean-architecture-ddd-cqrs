<?php

namespace Domain\Repositories;

use Domain\Entities\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryContract
{
    public function save(string $name, string $email, string $password): string;

    public function findByEmail(string $email): ?User;

    public function getAllUsers(): Collection;

    public function update(int $id, string $name, string $email): int;
}
