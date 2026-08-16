<?php

declare(strict_types=1);

namespace Shared\Domain\ValueObject;

interface ValueObject
{
    public function equals(self $other): bool;
}
