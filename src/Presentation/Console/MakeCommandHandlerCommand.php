<?php

declare(strict_types=1);

namespace Presentation\Console;

use Illuminate\Console\Command;
use Presentation\Console\Support\RendersDddStub;

final class MakeCommandHandlerCommand extends Command
{
    use RendersDddStub;

    protected $signature = 'make:command-handler {name : Class name, e.g. ShipOrder} {module : Bounded context, e.g. Ordering}';

    protected $description = 'Create a Command and its handler under Application\{module}';

    public function handle(): int
    {
        $module = str($this->argument('module'))->studly()->toString();
        $name = str($this->argument('name'))->studly()->toString();
        $command = str($name)->finish('Command')->toString();
        $handler = $command . 'Handler';

        $commandCreated = $this->renderStub(
            command: $this,
            stubName: 'command',
            targetPath: base_path("src/Application/{$module}/Commands/{$command}.php"),
            replacements: ['module' => $module, 'class' => $command],
        );

        $handlerCreated = $this->renderStub(
            command: $this,
            stubName: 'command-handler',
            targetPath: base_path("src/Application/{$module}/CommandHandlers/{$handler}.php"),
            replacements: ['module' => $module, 'class' => $handler, 'command' => $command],
        );

        if ($commandCreated || $handlerCreated) {
            $this->comment('Run "php artisan bus:cache" (or just dispatch it — discovery is on-demand in local dev) to pick up the new handler.');
        }

        return ($commandCreated && $handlerCreated) ? self::SUCCESS : self::FAILURE;
    }
}
