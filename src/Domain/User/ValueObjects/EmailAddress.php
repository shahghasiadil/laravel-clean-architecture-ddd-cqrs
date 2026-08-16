<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use Shared\Domain\Exceptions\InvalidValueObjectException;
use Shared\Domain\ValueObject\StringValueObject;

final class EmailAddress extends StringValueObject
{
    protected function guard(string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidValueObjectException("\"{$value}\" is not a valid email address.");
        }
    }
}
