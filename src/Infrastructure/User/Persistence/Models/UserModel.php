<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Models;

use Domain\User\Enums\UserStatus;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Infrastructure\User\Persistence\Factories\UserModelFactory;
use Infrastructure\User\Persistence\Observers\UserObserver;

/**
 * Eloquent persistence model for a user. This is an Infrastructure concern —
 * see Domain\User\Entities\User for the framework-free aggregate root and
 * Infrastructure\User\Persistence\Mappers\UserMapper for the translation
 * between the two.
 */
#[ObservedBy([UserObserver::class])]
class UserModel extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function newFactory(): UserModelFactory
    {
        return UserModelFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }
}
