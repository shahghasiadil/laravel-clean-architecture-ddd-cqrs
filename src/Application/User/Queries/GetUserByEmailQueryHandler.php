<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Attributes\AsQueryHandler;
use Application\Bus\QueryHandler;
use Domain\User\Entities\User;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Repositories\UserRepositoryContract;

#[AsQueryHandler(GetUserByEmailQuery::class)]
final readonly class GetUserByEmailQueryHandler implements QueryHandler
{
    public function __construct(
        private UserRepositoryContract $userRepository,
    ) {}

    public function handle(GetUserByEmailQuery $query): User
    {
        return $this->userRepository->findByEmail($query->email)
            ?? throw UserNotFoundException::withEmail($query->email->value);
    }
}
