<?php

declare(strict_types=1);

namespace Presentation\Console;

use Application\Bus\HandlerRegistry;
use Illuminate\Console\Command;

final class BusCacheCommand extends Command
{
    protected $signature = 'bus:cache';

    protected $description = 'Discover command/query handlers via attributes and cache the map for production';

    public function handle(HandlerRegistry $registry): int
    {
        $map = $registry->warm();

        $this->info(sprintf(
            'Cached %d command handler(s) and %d query handler(s).',
            count($map['commands']),
            count($map['queries']),
        ));

        return self::SUCCESS;
    }
}
