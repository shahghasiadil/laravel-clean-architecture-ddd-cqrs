<?php

declare(strict_types=1);

namespace Domain\User\Events;

use Domain\User\ValueObjects\EmailAddress;
use Domain\User\ValueObjects\UserId;
use Shared\Domain\DomainEvent;

final class UserRegistered extends DomainEvent
{
    public function __construct(
        public readonly UserId $userId,
        public readonly EmailAddress $email,
    ) {
        parent::__construct();
    }
}
