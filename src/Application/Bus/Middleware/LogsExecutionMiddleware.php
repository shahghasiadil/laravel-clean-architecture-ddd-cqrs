<?php

declare(strict_types=1);

namespace Application\Bus\Middleware;

use Application\Bus\Command;
use Application\Bus\Query;
use Closure;
use Psr\Log\LoggerInterface;

final readonly class LogsExecutionMiddleware
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function handle(Command|Query $message, Closure $next): mixed
    {
        $start = microtime(true);

        try {
            return $next($message);
        } finally {
            $this->logger->debug('bus.dispatched', [
                'message' => $message::class,
                'duration_ms' => round((microtime(true) - $start) * 1000, 2),
            ]);
        }
    }
}
