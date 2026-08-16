<?php

declare(strict_types=1);

use Domain\User\ValueObjects\UserId;
use Shared\Domain\Exceptions\DomainValidationException;
use Shared\Domain\Exceptions\InvalidValueObjectException;

it('accepts a positive integer', function (): void {
    expect((new UserId(1))->value)->toBe(1);
});

it('rejects a non-positive id', function (): void {
    expect(fn() => new UserId(0))->toThrow(InvalidValueObjectException::class);
    expect(fn() => new UserId(-1))->toThrow(InvalidValueObjectException::class);
});

it('is a DomainValidationException, so it renders as a 422 problem-details response', function (): void {
    expect(fn() => new UserId(0))->toThrow(DomainValidationException::class);
});
