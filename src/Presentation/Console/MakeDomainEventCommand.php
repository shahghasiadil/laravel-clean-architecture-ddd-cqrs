<?php

declare(strict_types=1);

namespace Presentation\Console;

use Illuminate\Console\Command;
use Presentation\Console\Support\RendersDddStub;

final class MakeDomainEventCommand extends Command
{
    use RendersDddStub;

    protected $signature = 'make:domain-event {name : Class name, e.g. OrderShipped} {module : Bounded context, e.g. Ordering}';

    protected $description = 'Create a Domain event under Domain\{module}\Events';

    public function handle(): int
    {
        $module = str($this->argument('module'))->studly()->toString();
        $class = str($this->argument('name'))->studly()->toString();

        $created = $this->renderStub(
            command: $this,
            stubName: 'domain-event',
            targetPath: base_path("src/Domain/{$module}/Events/{$class}.php"),
            replacements: [
                'module' => $module,
                'class' => $class,
            ],
        );

        return $created ? self::SUCCESS : self::FAILURE;
    }
}
