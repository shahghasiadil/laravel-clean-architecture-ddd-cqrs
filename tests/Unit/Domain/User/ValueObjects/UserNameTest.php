<?php

declare(strict_types=1);

use Domain\User\ValueObjects\UserName;
use Shared\Domain\Exceptions\InvalidValueObjectException;

it('accepts a name of at least 3 characters', function (): void {
    expect((new UserName('Ada'))->value)->toBe('Ada');
});

it('rejects a name shorter than 3 characters', function (): void {
    expect(fn(): UserName => new UserName('Ad'))->toThrow(InvalidValueObjectException::class);
});
