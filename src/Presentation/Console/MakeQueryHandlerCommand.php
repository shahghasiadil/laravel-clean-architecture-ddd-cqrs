<?php

declare(strict_types=1);

namespace Presentation\Console;

use Illuminate\Console\Command;
use Presentation\Console\Support\RendersDddStub;

final class MakeQueryHandlerCommand extends Command
{
    use RendersDddStub;

    protected $signature = 'make:query-handler {name : Class name, e.g. FindOrderById} {module : Bounded context, e.g. Ordering}';

    protected $description = 'Create a Query and its handler under Application\{module}\Queries';

    public function handle(): int
    {
        $module = str($this->argument('module'))->studly()->toString();
        $name = str($this->argument('name'))->studly()->toString();
        $query = str($name)->finish('Query')->toString();
        $handler = $query . 'Handler';

        $queryCreated = $this->renderStub(
            command: $this,
            stubName: 'query',
            targetPath: base_path("src/Application/{$module}/Queries/{$query}.php"),
            replacements: ['module' => $module, 'class' => $query],
        );

        $handlerCreated = $this->renderStub(
            command: $this,
            stubName: 'query-handler',
            targetPath: base_path("src/Application/{$module}/Queries/{$handler}.php"),
            replacements: ['module' => $module, 'class' => $handler, 'query' => $query],
        );

        if ($queryCreated || $handlerCreated) {
            $this->comment('Run "php artisan bus:cache" (or just ask it — discovery is on-demand in local dev) to pick up the new handler.');
        }

        return ($queryCreated && $handlerCreated) ? self::SUCCESS : self::FAILURE;
    }
}
