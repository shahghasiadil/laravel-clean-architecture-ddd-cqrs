<?php

declare(strict_types=1);

namespace Application\User\DTOs;

final class CreateUserDTO
{
    public function __construct(public string $name, public string $email, public string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

}
