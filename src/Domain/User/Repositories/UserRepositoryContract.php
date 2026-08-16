<?php

declare(strict_types=1);

namespace Domain\User\Repositories;

use Domain\User\Entities\User;
use Domain\User\ValueObjects\EmailAddress;
use Domain\User\ValueObjects\UserId;
use Shared\Domain\Paginated;

interface UserRepositoryContract
{
    /**
     * Persists a new user or the changes made to an existing one, assigning
     * an identity via User::assignId() on first insert.
     */
    public function save(User $user): void;

    public function findByEmail(EmailAddress $email): ?User;

    public function findById(UserId $id): ?User;

    /**
     * @return Paginated<User>
     */
    public function paginate(int $page, int $perPage): Paginated;
}
