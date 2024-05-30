<?php

declare(strict_types=1);

namespace Application\User\Commands;

use Application\Bus\Command;
use Application\User\DTOs\CreateUserDTO;

final class CreateUserCommand extends Command
{
    public function __construct(
        public CreateUserDTO $CreateUserDTO,
    ) {}
}
