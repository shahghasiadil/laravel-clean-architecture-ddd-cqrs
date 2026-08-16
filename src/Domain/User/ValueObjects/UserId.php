<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use Shared\Domain\Exceptions\InvalidValueObjectException;
use Shared\Domain\ValueObject\IntValueObject;

final class UserId extends IntValueObject
{
    protected function guard(int $value): void
    {
        if ($value < 1) {
            throw new InvalidValueObjectException('A user id must be a positive integer.');
        }
    }
}
