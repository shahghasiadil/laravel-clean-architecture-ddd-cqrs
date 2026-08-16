<?php

declare(strict_types=1);

namespace Presentation\Console;

use Illuminate\Console\Command;

/**
 * Scaffolds a full four-layer bounded-context slice in one shot: an
 * aggregate + repository contract, a Create<Entity> command/handler, a
 * Get<Entity>ById query/handler, and a routes file — everything a new
 * bounded context needs to exist, minus the business rules themselves.
 */
final class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : Bounded context name, e.g. Billing}';

    protected $description = 'Scaffold a new bounded context: aggregate, repository contract, one command, one query, and a routes file';

    public function handle(): int
    {
        $module = str($this->argument('name'))->studly()->toString();
        $entity = $module; // The module's primary aggregate defaults to the module's own name.

        $this->call('make:aggregate', ['name' => $entity, 'module' => $module]);
        $this->call('make:command-handler', ['name' => "Create{$entity}", 'module' => $module]);
        $this->call('make:query-handler', ['name' => "Get{$entity}ById", 'module' => $module]);

        $routesPath = base_path("src/Presentation/{$module}/routes/api.php");

        if ( ! is_dir(dirname($routesPath))) {
            mkdir(dirname($routesPath), 0755, true);
        }

        if ( ! is_file($routesPath)) {
            file_put_contents($routesPath, <<<PHP
                <?php

                declare(strict_types=1);

                use Illuminate\Support\Facades\Route;

                Route::prefix('v1')->middleware('throttle:api')->group(function (): void {
                    // TODO: register {$module} routes here.
                });

                PHP);
            $this->info('Created: ' . $routesPath);
        }

        $this->newLine();
        $this->comment('Next steps:');
        $this->line("  1. Fill in the {$entity} aggregate's properties and invariants.");
        $this->line("  2. Implement {$entity}RepositoryContract under Infrastructure\\{$module}\\Persistence.");
        $this->line("  3. Bind it in a service provider's \$singletons array (see Infrastructure\\Providers\\InfrastructureServiceProvider for the pattern).");
        $this->line("  4. Add \"{$routesPath}\" to the api: [...] array in bootstrap/app.php.");
        $this->line('  5. Add arch-test coverage for the new namespaces in tests/Architecture, matching the User module.');

        return self::SUCCESS;
    }
}
