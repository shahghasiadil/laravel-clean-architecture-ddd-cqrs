<?php

declare(strict_types=1);

namespace Application\Bus\Middleware;

use Application\Bus\Command;
use Application\Bus\IdempotentCommand;
use Closure;
use Illuminate\Contracts\Cache\Repository;

/**
 * No-ops for any command that doesn't opt in via IdempotentCommand — most
 * commands don't need this, and it would be wrong to cache their results by
 * default.
 */
final readonly class IdempotencyMiddleware
{
    private const string CACHE_PREFIX = 'bus:idempotency:';

    private const int TTL_SECONDS = 86400;

    public function __construct(
        private Repository $cache,
    ) {}

    public function handle(Command $command, Closure $next): mixed
    {
        if (! $command instanceof IdempotentCommand) {
            return $next($command);
        }

        $key = self::CACHE_PREFIX . $command->idempotencyKey();

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $result = $next($command);

        $this->cache->put($key, $result, self::TTL_SECONDS);

        return $result;
    }
}
