<?php

declare(strict_types=1);

namespace Application\User\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface UserServiceContract
{
    public function index(): Collection;
}
