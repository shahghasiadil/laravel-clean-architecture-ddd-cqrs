<?php

declare(strict_types=1);

namespace Application\User\Services;

use Application\User\Contracts\UserServiceContract;
use Domain\Repositories\UserRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

class UserService implements UserServiceContract
{
    public function __construct(private UserRepositoryContract $userRepository)
    {

    }

    public function index(): Collection
    {
        return $this->userRepository->getAllUsers();
    }
}
