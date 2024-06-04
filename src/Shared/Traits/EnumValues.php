<?php

declare(strict_types=1);

namespace Shared\Traits;

trait EnumValues
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function forSelect(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
