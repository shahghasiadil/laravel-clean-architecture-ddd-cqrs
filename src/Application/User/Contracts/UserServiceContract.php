<?php

declare(strict_types=1);

namespace Application\User\Contracts;

use Domain\Entities\User;

interface UserServiceContract
{
    public function index(): ?User;
}
