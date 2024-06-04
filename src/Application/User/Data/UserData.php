<?php

declare(strict_types=1);

namespace Application\User\Data;

use Shared\Enums\UserStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public UserStatus $status,
        public string|Optional $password,
    ) {}
}
