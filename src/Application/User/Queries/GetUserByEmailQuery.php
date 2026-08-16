<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query;
use Domain\User\ValueObjects\EmailAddress;

/**
 * @implements Query<\Domain\User\Entities\User>
 */
final readonly class GetUserByEmailQuery implements Query
{
    public function __construct(
        public EmailAddress $email,
    ) {}
}
