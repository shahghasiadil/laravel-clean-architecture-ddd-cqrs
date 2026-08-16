<?php

declare(strict_types=1);

namespace Application\User\Data;

use Domain\User\Enums\UserStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Write-side DTO carrying validated request input into a command. Password
 * is optional so an update can change a user's name or status without
 * forcing the caller to resubmit a password.
 */
class UserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public UserStatus $status,
        public string|Optional $password,
    ) {}
}
