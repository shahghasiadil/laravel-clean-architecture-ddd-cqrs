<?php

declare(strict_types=1);

use Domain\User\ValueObjects\EmailAddress;
use Shared\Domain\Exceptions\InvalidValueObjectException;

it('accepts a valid email address', function (): void {
    expect((new EmailAddress('ada@example.com'))->value)->toBe('ada@example.com');
});

it('rejects an invalid email address', function (): void {
    expect(fn(): EmailAddress => new EmailAddress('not-an-email'))->toThrow(InvalidValueObjectException::class);
});

it('two value objects with the same value are equal', function (): void {
    expect(new EmailAddress('ada@example.com')->equals(new EmailAddress('ada@example.com')))->toBeTrue();
    expect(new EmailAddress('ada@example.com')->equals(new EmailAddress('grace@example.com')))->toBeFalse();
});
