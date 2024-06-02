<?php

declare(strict_types=1);

namespace Application\User\Data;

use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(public int $id, public string $name, public string $email, public string $password)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}
