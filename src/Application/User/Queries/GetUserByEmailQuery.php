<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query;

class GetUserByEmailQuery extends Query
{
    public function __construct(
        private readonly string $email,
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }
}
