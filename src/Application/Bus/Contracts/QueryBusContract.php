<?php

declare(strict_types=1);

namespace Application\Bus\Contracts;

use Application\Bus\Query;

interface QueryBusContract
{
    /**
     * @template TResult
     *
     * @param  Query<TResult>  $query
     * @return TResult
     */
    public function ask(Query $query): mixed;
}
