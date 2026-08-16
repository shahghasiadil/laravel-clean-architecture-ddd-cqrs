<?php

declare(strict_types=1);

namespace Application\User\Commands;

use Application\Bus\Command;
use Application\User\Data\UserData;
use Domain\User\ValueObjects\UserId;

final readonly class UpdateUserCommand implements Command
{
    public function __construct(
        public UserId $id,
        public UserData $userData,
    ) {}
}
