<?php

declare(strict_types=1);

namespace Application\User\Commands;

use Application\Bus\Command;
use Application\User\Data\UserData;

final readonly class CreateUserCommand implements Command
{
    public function __construct(
        public UserData $userData,
    ) {}
}
