<?php

declare(strict_types=1);

namespace Application\User\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class UsersListData extends Data
{
    public int $id;
    public string $name;
    public array $properties;
    public CarbonImmutable $created_at;
    public CarbonImmutable $updated_at;
}
