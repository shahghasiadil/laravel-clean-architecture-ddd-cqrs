<?php

declare(strict_types=1);

namespace Presentation\Console\Support;

use Illuminate\Console\Command;
use RuntimeException;

/**
 * Shared plumbing for the make:* DDD generator commands: renders a stub
 * from stubs/ddd/*.stub into src/, replacing {{ placeholders }}.
 */
trait RendersDddStub
{
    /**
     * @param  array<string, string>  $replacements
     */
    protected function renderStub(Command $command, string $stubName, string $targetPath, array $replacements): bool
    {
        $stubPath = base_path("stubs/ddd/{$stubName}.stub");

        if (! is_file($stubPath)) {
            throw new RuntimeException("Missing stub: {$stubPath}");
        }

        if (is_file($targetPath)) {
            $command->error("Already exists: {$targetPath}");

            return false;
        }

        $contents = file_get_contents($stubPath);

        foreach ($replacements as $search => $replace) {
            $contents = str_replace('{{ ' . $search . ' }}', $replace, (string) $contents);
        }

        $directory = dirname($targetPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($targetPath, $contents);

        $command->info('Created: ' . $targetPath);

        return true;
    }
}
