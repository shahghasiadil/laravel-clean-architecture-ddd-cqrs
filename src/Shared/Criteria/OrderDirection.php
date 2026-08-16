<?php

declare(strict_types=1);

namespace Shared\Criteria;

enum OrderDirection: string
{
    case Asc = 'asc';
    case Desc = 'desc';
}
