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

    /**
     * Converts the DTO properties to an associative array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
