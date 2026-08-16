<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use Shared\Domain\Exceptions\InvalidValueObjectException;
use Shared\Domain\ValueObject\StringValueObject;

/**
 * Wraps a password that has already been hashed. The Domain never sees
 * plain-text passwords or hashing algorithms — that is an Infrastructure
 * concern, performed before this value object is constructed.
 */
final class HashedPassword extends StringValueObject
{
    protected function guard(string $value): void
    {
        if ($value === '') {
            throw new InvalidValueObjectException('A hashed password cannot be empty.');
        }
    }
}
