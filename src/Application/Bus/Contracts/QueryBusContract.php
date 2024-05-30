<?php

declare(strict_types=1);

namespace Application\Bus\Contracts;

use Application\Bus\Query;

interface QueryBusContract
{
    public function ask(Query $query): mixed;

    public function register(array $map): void;
}
