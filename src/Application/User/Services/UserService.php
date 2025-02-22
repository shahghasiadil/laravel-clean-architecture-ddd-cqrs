<?php

declare(strict_types=1);

namespace Application\User\Services;

use Application\User\Contracts\UserServiceContract;
use Application\User\Data\UserData;
use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

class UserService implements UserServiceContract
{
    public function __construct(private readonly UserRepositoryContract $userRepository) {}

    public function index(): Collection
    {
        return $this->userRepository->getAllUsers();
    }

    public function update($id, UserData $userData): bool
    {
        return $this->userRepository->update($id, $userData);
    }
}
