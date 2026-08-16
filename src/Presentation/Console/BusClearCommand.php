<?php

declare(strict_types=1);

namespace Presentation\Console;

use Application\Bus\HandlerRegistry;
use Illuminate\Console\Command;

final class BusClearCommand extends Command
{
    protected $signature = 'bus:clear';

    protected $description = 'Clear the cached command/query handler map';

    public function handle(HandlerRegistry $registry): int
    {
        $registry->clearCache();

        $this->info('Handler cache cleared.');

        return self::SUCCESS;
    }
}
