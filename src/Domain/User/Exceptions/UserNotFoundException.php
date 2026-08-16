<?php

declare(strict_types=1);

namespace Domain\User\Exceptions;

use Shared\Domain\Exceptions\EntityNotFoundException;

final class UserNotFoundException extends EntityNotFoundException
{
    public static function withId(int $id): self
    {
        return new self("User with id \"{$id}\" was not found.");
    }

    public static function withEmail(string $email): self
    {
        return new self("User with email \"{$email}\" was not found.");
    }
}
