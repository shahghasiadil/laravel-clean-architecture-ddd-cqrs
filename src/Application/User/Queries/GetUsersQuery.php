<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query;

/**
 * @implements Query<\Shared\Domain\Paginated<\Domain\User\Entities\User>>
 */
final readonly class GetUsersQuery implements Query
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
    ) {}
}
