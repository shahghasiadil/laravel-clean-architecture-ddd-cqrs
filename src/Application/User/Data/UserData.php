<?php

declare(strict_types=1);

namespace Application\User\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string|Optional $password,
    ) {}
}
