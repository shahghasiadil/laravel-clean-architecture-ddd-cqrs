<?php

namespace Domain\Repositories;

use Domain\Entities\User;

interface UserRepositoryContract
{
    public function save(string $name, string $email, string $password): string;

    public function findByEmail(string $email): ?User;

    public function getAllUsers(): User;
}
