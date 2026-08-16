<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Policies;

use Infrastructure\User\Persistence\Models\UserModel;

/**
 * Laravel Gate authorization for the User resource. This binds to the
 * Eloquent model — a framework concept — so it lives in Infrastructure
 * rather than Domain, which stays framework-free.
 *
 * Not yet wired into the demo API routes — those are intentionally open so
 * the template runs without an auth setup out of the box — but ready to
 * gate behind auth:sanctum once real authentication is added, e.g. by
 * calling Gate::authorize('update', $targetUser) from a form request.
 */
final class UserPolicy
{
    public function viewAny(UserModel $actor): bool
    {
        return true;
    }

    public function view(UserModel $actor, UserModel $target): bool
    {
        return true;
    }

    public function create(UserModel $actor): bool
    {
        return true;
    }

    public function update(UserModel $actor, UserModel $target): bool
    {
        return $actor->is($target);
    }

    public function delete(UserModel $actor, UserModel $target): bool
    {
        return $actor->is($target);
    }
}
