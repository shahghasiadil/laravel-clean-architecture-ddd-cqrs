<?php

declare(strict_types=1);

namespace Application\Bus;

/**
 * Opt-in marker for a Command whose result should be memoised by key, so a
 * client's retried request (double form submission, at-least-once delivery)
 * re-plays the original result instead of re-running the handler.
 */
interface IdempotentCommand extends Command
{
    public function idempotencyKey(): string;
}
