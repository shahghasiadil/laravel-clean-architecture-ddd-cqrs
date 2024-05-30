<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\QueryHandler;
use Domain\User\Repositories\UserRepositoryContract;

class GetUserByEmailQueryHandler extends QueryHandler
{
    public function __construct(
        protected readonly UserRepositoryContract $repository,
    ) {}

    public function handle(GetUserByEmailQuery $query): ?object
    {
        return $this->repository->findByEmail(
            $query->getEmail(),
        );
    }
}
