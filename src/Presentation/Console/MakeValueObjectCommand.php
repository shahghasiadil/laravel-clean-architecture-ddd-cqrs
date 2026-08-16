<?php

declare(strict_types=1);

namespace Presentation\Console;

use Illuminate\Console\Command;
use Presentation\Console\Support\RendersDddStub;

final class MakeValueObjectCommand extends Command
{
    use RendersDddStub;

    protected $signature = 'make:value-object {name : Class name, e.g. Money} {module : Bounded context, e.g. Billing} {--int : Wrap an int instead of a string}';

    protected $description = 'Create a Domain value object under Domain\{module}\ValueObjects';

    public function handle(): int
    {
        $module = str($this->argument('module'))->studly()->toString();
        $class = str($this->argument('name'))->studly()->toString();

        $created = $this->renderStub(
            command: $this,
            stubName: 'value-object',
            targetPath: base_path("src/Domain/{$module}/ValueObjects/{$class}.php"),
            replacements: [
                'module' => $module,
                'class' => $class,
                'baseClass' => $this->option('int') ? 'IntValueObject' : 'StringValueObject',
                'paramType' => $this->option('int') ? 'int' : 'string',
            ],
        );

        return $created ? self::SUCCESS : self::FAILURE;
    }
}
