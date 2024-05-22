<?php

namespace Application\Bus;

interface QueryBusContract
{

    public function ask(Query $query): mixed;

    public function register(array $map): void;

}
