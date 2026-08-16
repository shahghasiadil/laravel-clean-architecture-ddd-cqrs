<?php

declare(strict_types=1);

namespace Shared\Domain\ValueObject;

abstract class IntValueObject implements ValueObject
{
    public function __construct(
        public readonly int $value,
    ) {
        $this->guard($value);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof static && $other->value === $this->value;
    }

    protected function guard(int $value): void {}
}
