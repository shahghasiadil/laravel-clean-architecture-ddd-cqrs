<?php

declare(strict_types=1);

namespace Application\User\Commands;

use Application\Bus\Command;
use Application\User\Data\UserData;

final class CreateUserCommand extends Command
{
    public function __construct(
        public UserData $userData,
    ) {}
}
