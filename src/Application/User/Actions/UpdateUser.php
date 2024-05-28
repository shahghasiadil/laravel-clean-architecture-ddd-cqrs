<?php

namespace Application\User\Actions;

use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Support\Facades\DB;

class UpdateUser
{
    public function __construct(private UserRepositoryContract $userRepository)
    {
    }

    public function __invoke(int $id, string $name, string $email)
    {
        return DB::transaction(function () use ($id, $name, $email) {
           return $this->userRepository->update($id, $name, $email);
        });

    }
}
