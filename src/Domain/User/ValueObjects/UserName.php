<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use Shared\Domain\Exceptions\InvalidValueObjectException;
use Shared\Domain\ValueObject\StringValueObject;

final class UserName extends StringValueObject
{
    private const int MIN_LENGTH = 3;

    protected function guard(string $value): void
    {
        if (mb_strlen(mb_trim($value)) < self::MIN_LENGTH) {
            throw new InvalidValueObjectException('A user name must be at least ' . self::MIN_LENGTH . ' characters long.');
        }
    }
}
