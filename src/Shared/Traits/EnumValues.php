<?php

namespace Shared\Enums\Traits;

trait EnumValues {
    public static function values(): array
    {
       return array_column(self::cases(), 'value');
    }

    public static function forSelect(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
