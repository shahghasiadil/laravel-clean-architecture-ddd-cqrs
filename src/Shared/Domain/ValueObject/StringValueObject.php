<?php

declare(strict_types=1);

namespace Shared\Domain\ValueObject;

abstract class StringValueObject implements ValueObject
{
    public function __construct(
        public readonly string $value,
    ) {
        $this->guard($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof static && $other->value === $this->value;
    }

    protected function guard(string $value): void {}
}
