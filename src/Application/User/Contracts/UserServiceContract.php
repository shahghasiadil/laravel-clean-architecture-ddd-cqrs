<?php

declare(strict_types=1);

namespace Application\User\Contracts;

use Application\User\Data\UserData;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceContract
{
    public function index(): Collection;

    public function update($id, UserData $userData): bool;
}
