<?php

declare(strict_types=1);

namespace Domain\User\Repositories;

use Application\User\DTOs\CreateUserDTO;
use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryContract
{
    public function save(CreateUserDTO $dto): string;

    public function findByEmail(string $email): ?User;

    public function getAllUsers(): Collection;

    public function update(int $id, string $name, string $email): int;
}
