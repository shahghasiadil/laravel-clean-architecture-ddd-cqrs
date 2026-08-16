<?php

declare(strict_types=1);

namespace Shared\Domain\ValueObject;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Shared\Domain\Exceptions\InvalidValueObjectException;

final class Uuid extends StringValueObject
{
    public static function generate(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    protected function guard(string $value): void
    {
        if ( ! RamseyUuid::isValid($value)) {
            throw new InvalidValueObjectException("\"{$value}\" is not a valid UUID.");
        }
    }
}
