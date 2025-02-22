<?php

declare(strict_types=1);

namespace Domain\User\Enums;

use Domain\Common\Traits\EnumValues;

enum UserStatus: string
{
    use EnumValues;

    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}
