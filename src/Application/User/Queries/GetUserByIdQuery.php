<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query;

class GetUserByIdQuery extends Query
{
    public function __construct(
        private readonly int $id,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
}
