<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Attributes\AsQueryHandler;
use Application\Bus\QueryHandler;
use Domain\User\Entities\User;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Repositories\UserRepositoryContract;

#[AsQueryHandler(GetUserByIdQuery::class)]
final readonly class GetUserByIdQueryHandler implements QueryHandler
{
    public function __construct(
        private UserRepositoryContract $userRepository,
    ) {}

    public function handle(GetUserByIdQuery $query): User
    {
        return $this->userRepository->findById($query->id)
            ?? throw UserNotFoundException::withId($query->id->value);
    }
}
