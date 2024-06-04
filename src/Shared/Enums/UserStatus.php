<?php

declare(strict_types=1);

namespace Shared\Enums;

use Shared\Traits\EnumValues;

enum UserStatus: string
{
    use EnumValues;

    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}
