<?php

namespace Application\DTOs\User;

class UserDTO
{
    public function __construct(public string $name, public string $email, public string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}
