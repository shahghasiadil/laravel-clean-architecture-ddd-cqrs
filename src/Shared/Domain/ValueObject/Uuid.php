<?php

declare(strict_types=1);

namespace Shared\Domain\ValueObject;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Shared\Domain\Exceptions\InvalidValueObjectException;

class Uuid extends StringValueObject
{
    public static function generate(): static
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    protected function guard(string $value): void
    {
        if (! RamseyUuid::isValid($value)) {
            throw new InvalidValueObjectException("\"{$value}\" is not a valid UUID.");
        }
    }
}
