<?php

declare(strict_types=1);

namespace Presentation\Console;

use Illuminate\Console\Command;
use Presentation\Console\Support\RendersDddStub;

final class MakeAggregateCommand extends Command
{
    use RendersDddStub;

    protected $signature = 'make:aggregate {name : Class name, e.g. Order} {module : Bounded context, e.g. Ordering}';

    protected $description = 'Create a Domain aggregate root and its repository contract under Domain\{module}';

    public function handle(): int
    {
        $module = str($this->argument('module'))->studly()->toString();
        $entity = str($this->argument('name'))->studly()->toString();
        $repositoryContract = $entity . 'RepositoryContract';

        $aggregateCreated = $this->renderStub(
            command: $this,
            stubName: 'aggregate',
            targetPath: base_path("src/Domain/{$module}/Entities/{$entity}.php"),
            replacements: ['module' => $module, 'class' => $entity],
        );

        $repositoryCreated = $this->renderStub(
            command: $this,
            stubName: 'repository-contract',
            targetPath: base_path("src/Domain/{$module}/Repositories/{$repositoryContract}.php"),
            replacements: [
                'module' => $module,
                'class' => $repositoryContract,
                'entity' => $entity,
                'entityVariable' => str($entity)->camel()->toString(),
            ],
        );

        if ($aggregateCreated || $repositoryCreated) {
            $this->comment("Next: implement {$repositoryContract} under Infrastructure\\{$module}\\Persistence, and bind it in a service provider's \$singletons array.");
        }

        return ($aggregateCreated && $repositoryCreated) ? self::SUCCESS : self::FAILURE;
    }
}
