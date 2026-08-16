<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Observers;

use Infrastructure\User\Persistence\Models\UserModel;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(UserModel $user): void
    {
        // ...
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(UserModel $user): void
    {
        // ...
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(UserModel $user): void
    {
        // ...
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(UserModel $user): void
    {
        // ...
    }

    /**
     * Handle the User "forceDeleted" event.
     */
    public function forceDeleted(UserModel $user): void
    {
        // ...
    }
}
