<?php

declare(strict_types=1);

namespace Shared\Domain\Exceptions;

/**
 * A malformed constructor argument to a value object (e.g. UserId(0), an
 * invalid email string) — semantically a domain validation failure, so it
 * inherits DomainValidationException's 422 mapping in bootstrap/app.php
 * rather than needing its own render() rule.
 */
class InvalidValueObjectException extends DomainValidationException {}
