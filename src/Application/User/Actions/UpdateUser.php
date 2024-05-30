<?php

declare(strict_types=1);

namespace Application\User\Actions;

use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Support\Facades\DB;

class UpdateUser
{
    public function __construct(private UserRepositoryContract $userRepository) {}

    public function __invoke(int $id, string $name, string $email)
    {
        return DB::transaction(fn() => $this->userRepository->update($id, $name, $email));

    }
}
