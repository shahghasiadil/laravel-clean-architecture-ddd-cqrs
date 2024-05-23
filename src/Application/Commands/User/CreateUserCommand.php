<?php

namespace Application\Commands\User;

use Application\Bus\Command;
use Application\DTOs\User\UserDTO;

class CreateUserCommand extends Command
{
    public function __construct(
        public UserDTO $userDTO
    ) {
    }
}
