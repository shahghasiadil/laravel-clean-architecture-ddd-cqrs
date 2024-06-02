<?php

declare(strict_types=1);

namespace Application\User\DTOs;

class UserDTO
{
    public function __construct(public int $id, public string $name, public string $email, public string $password)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}
