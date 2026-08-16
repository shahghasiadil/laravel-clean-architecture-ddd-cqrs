<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query;
use Domain\User\ValueObjects\UserId;

/**
 * @implements Query<\Domain\User\Entities\User>
 */
final readonly class GetUserByIdQuery implements Query
{
    public function __construct(
        public UserId $id,
    ) {}
}
