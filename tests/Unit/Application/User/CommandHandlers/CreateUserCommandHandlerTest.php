<?php

declare(strict_types=1);

use Application\User\CommandHandlers\CreateUserCommandHandler;
use Application\User\Commands\CreateUserCommand;
use Application\User\Data\UserData;
use Domain\User\Entities\User;
use Domain\User\Enums\UserStatus;
use Domain\User\Repositories\UserRepositoryContract;
use Domain\User\ValueObjects\EmailAddress;
use Domain\User\ValueObjects\UserId;
use Shared\Domain\Exceptions\DomainValidationException;
use Shared\Domain\Paginated;
use Spatie\LaravelData\Optional;

// The handler hashes the password via Laravel's Hash facade, so this file
// needs the application container booted (no database access, hence no
// RefreshDatabase) — unlike the pure-PHP tests in tests/Unit/Domain.
uses(Tests\TestCase::class);

/**
 * A minimal in-memory double — no database, no container — so this handler
 * can be unit tested in isolation from Infrastructure.
 */
final class InMemoryUserRepository implements UserRepositoryContract
{
    /** @var array<int, User> */
    public array $saved = [];
    private int $nextId = 1;

    public function save(User $user): void
    {
        if ( ! $user->hasId()) {
            $user->assignId(new UserId($this->nextId++));
        }

        $this->saved[$user->id()->value] = $user;
    }

    public function findByEmail(EmailAddress $email): ?User
    {
        foreach ($this->saved as $user) {
            if ($user->email()->equals($email)) {
                return $user;
            }
        }

        return null;
    }

    public function findById(UserId $id): ?User
    {
        return $this->saved[$id->value] ?? null;
    }

    public function paginate(int $page, int $perPage): Paginated
    {
        return new Paginated(array_values($this->saved), count($this->saved), $perPage, $page);
    }
}

it('registers a user and assigns it an id', function (): void {
    $repository = new InMemoryUserRepository();
    $handler = new CreateUserCommandHandler($repository);

    $userData = new UserData('Ada Lovelace', 'ada@example.com', UserStatus::Active, 'plain-text-password');

    $id = $handler->handle(new CreateUserCommand($userData));

    expect($id)->toBeInstanceOf(UserId::class);
    expect($repository->findById($id))->not->toBeNull();
    expect($repository->findById($id)->password()->value)->not->toBe('plain-text-password');
});

it('honours the requested status instead of always registering as Active', function (): void {
    $repository = new InMemoryUserRepository();
    $handler = new CreateUserCommandHandler($repository);

    $userData = new UserData('Ada Lovelace', 'ada@example.com', UserStatus::Suspended, 'plain-text-password');

    $id = $handler->handle(new CreateUserCommand($userData));

    expect($repository->findById($id)->status())->toBe(UserStatus::Suspended);
});

it('refuses to register a user without a password', function (): void {
    $repository = new InMemoryUserRepository();
    $handler = new CreateUserCommandHandler($repository);

    $userData = new UserData('Ada Lovelace', 'ada@example.com', UserStatus::Active, new Optional());

    expect(fn() => $handler->handle(new CreateUserCommand($userData)))
        ->toThrow(DomainValidationException::class);
});
