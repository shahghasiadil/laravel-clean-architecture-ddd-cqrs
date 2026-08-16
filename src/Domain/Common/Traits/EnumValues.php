<?php

declare(strict_types=1);

namespace Domain\Common\Traits;

trait EnumValues
{
    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string>
     */
    public static function forSelect(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
