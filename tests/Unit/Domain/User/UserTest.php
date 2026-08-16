<?php

declare(strict_types=1);

use Domain\User\Entities\User;
use Domain\User\Enums\UserStatus;
use Domain\User\Events\UserRegistered;
use Domain\User\ValueObjects\EmailAddress;
use Domain\User\ValueObjects\HashedPassword;
use Domain\User\ValueObjects\UserId;
use Domain\User\ValueObjects\UserName;

beforeEach(function (): void {
    $this->user = User::register(
        new UserName('Ada Lovelace'),
        new EmailAddress('ada@example.com'),
        new HashedPassword('hashed-value'),
    );
});

it('is Active by default when registered', function (): void {
    expect($this->user->status())->toBe(UserStatus::Active);
});

it('has no id until one is assigned', function (): void {
    expect($this->user->hasId())->toBeFalse();
    expect(fn () => $this->user->id())->toThrow(LogicException::class);
});

it('records a UserRegistered event only once an id is assigned', function (): void {
    expect($this->user->releaseEvents())->toBeEmpty();

    $this->user->assignId(new UserId(1));

    $events = $this->user->releaseEvents();

    expect($events)->toHaveCount(1);
    expect($events[0])->toBeInstanceOf(UserRegistered::class);
    expect($this->user->releaseEvents())->toBeEmpty();
});

it('refuses to reassign an id', function (): void {
    $this->user->assignId(new UserId(1));

    expect(fn () => $this->user->assignId(new UserId(2)))->toThrow(LogicException::class);
});

it('renames and changes email, password, and status', function (): void {
    $this->user->rename(new UserName('Grace Hopper'));
    $this->user->changeEmail(new EmailAddress('grace@example.com'));
    $this->user->changePassword(new HashedPassword('new-hash'));
    $this->user->changeStatus(UserStatus::Suspended);

    expect($this->user->name()->value)->toBe('Grace Hopper');
    expect($this->user->email()->value)->toBe('grace@example.com');
    expect($this->user->password()->value)->toBe('new-hash');
    expect($this->user->status())->toBe(UserStatus::Suspended);
});
