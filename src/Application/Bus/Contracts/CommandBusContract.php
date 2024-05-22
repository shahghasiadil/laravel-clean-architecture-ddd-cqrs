<?php

namespace Application\Bus\Contracts;

use Application\Bus\Command;

interface CommandBusContract
{
    public function dispatch(Command $command): mixed;

    public function register(array $map): void;
}
