<?php

namespace Application\User\Commands;

use Application\Bus\Command;
use Application\User\DTOs\UserDTO;

class CreateUserCommand extends Command
{
    public function __construct(
        public UserDTO $userDTO
    ) {
    }
}
