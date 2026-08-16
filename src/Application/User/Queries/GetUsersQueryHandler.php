<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Attributes\AsQueryHandler;
use Application\Bus\QueryHandler;
use Domain\User\Entities\User;
use Domain\User\Repositories\UserRepositoryContract;
use Shared\Domain\Paginated;

#[AsQueryHandler(GetUsersQuery::class)]
final readonly class GetUsersQueryHandler implements QueryHandler
{
    public function __construct(
        private UserRepositoryContract $userRepository,
    ) {}

    /**
     * @return Paginated<User>
     */
    public function handle(GetUsersQuery $query): Paginated
    {
        return $this->userRepository->paginate($query->page, $query->perPage);
    }
}
