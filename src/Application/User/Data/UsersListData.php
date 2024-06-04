<?php

declare(strict_types=1);

namespace Application\User\Data;

use Carbon\Carbon;
use Shared\Enums\UserStatus;
use Spatie\LaravelData\Data;

class UsersListData extends Data
{
    public int $id;
    public string $name;
    public string $email;
    public UserStatus $status;
    public Carbon $created_at;
    public Carbon $updated_at;
}
