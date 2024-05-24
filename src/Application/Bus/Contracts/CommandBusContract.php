<?php

declare(strict_types=1);

namespace Application\Bus\Contracts;

use Application\Bus\Command;

interface CommandBusContract
{
    public function dispatch(Command $command): mixed;

    public function register(array $map): void;
}
