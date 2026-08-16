<?php

declare(strict_types=1);

namespace Application\Bus;

use Application\Bus\Attributes\AsCommandHandler;
use Application\Bus\Attributes\AsQueryHandler;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

/**
 * Discovers command and query handlers by scanning src/ for classes carrying
 * #[AsCommandHandler]/#[AsQueryHandler] attributes, so adding a use case
 * never means hand-editing a central provider map.
 *
 * The scan is only ever paid for once per deploy: warm() writes the
 * resulting map to bootstrap/cache/handlers.php via "php artisan bus:cache",
 * and resolve() prefers that cache when present.
 *
 * @phpstan-type HandlerMap array{commands: array<class-string, class-string>, queries: array<class-string, class-string>}
 */
final class HandlerRegistry
{
    /**
     * @var array<string, string>
     */
    private const array NAMESPACE_ROOTS = [
        'Application' => 'Application',
        'Domain' => 'Domain',
        'Infrastructure' => 'Infrastructure',
        'Presentation' => 'Presentation',
        'Shared' => 'Shared',
    ];

    /** @var HandlerMap|null */
    private ?array $map = null;

    public function __construct(
        private readonly string $sourcePath,
        private readonly ?string $cacheFile = null,
    ) {}

    /**
     * @return array<class-string, class-string>
     */
    public function commandMap(): array
    {
        return $this->resolve()['commands'];
    }

    /**
     * @return array<class-string, class-string>
     */
    public function queryMap(): array
    {
        return $this->resolve()['queries'];
    }

    /**
     * Forces a fresh scan and writes it to the cache file.
     *
     * @return HandlerMap
     */
    public function warm(): array
    {
        $map = $this->scan();

        if ($this->cacheFile !== null) {
            file_put_contents(
                $this->cacheFile,
                '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($map, true) . ';' . PHP_EOL,
            );
        }

        return $this->map = $map;
    }

    public function clearCache(): void
    {
        if ($this->cacheFile !== null && is_file($this->cacheFile)) {
            unlink($this->cacheFile);
        }

        $this->map = null;
    }

    /**
     * @return HandlerMap
     */
    private function resolve(): array
    {
        if ($this->map !== null) {
            return $this->map;
        }

        if ($this->cacheFile !== null && is_file($this->cacheFile)) {
            /** @var HandlerMap $cached */
            $cached = require $this->cacheFile;

            return $this->map = $cached;
        }

        return $this->map = $this->scan();
    }

    /**
     * @return HandlerMap
     */
    private function scan(): array
    {
        $commands = [];
        $queries = [];

        if (! is_dir($this->sourcePath)) {
            return ['commands' => $commands, 'queries' => $queries];
        }

        foreach ((new Finder())->files()->in($this->sourcePath)->name('*.php') as $file) {
            $class = $this->classFromPath($file->getRealPath());

            if ($class === null || ! class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);

            foreach ($reflection->getAttributes(AsCommandHandler::class) as $attribute) {
                $commands[$attribute->newInstance()->command] = $class;
            }

            foreach ($reflection->getAttributes(AsQueryHandler::class) as $attribute) {
                $queries[$attribute->newInstance()->query] = $class;
            }
        }

        return ['commands' => $commands, 'queries' => $queries];
    }

    private function classFromPath(string $realPath): ?string
    {
        $path = str_replace('\\', '/', $realPath);

        foreach (self::NAMESPACE_ROOTS as $namespace => $directory) {
            $needle = '/' . $directory . '/';

            if (! str_contains($path, $needle)) {
                continue;
            }

            $relative = substr($path, strpos($path, $needle) + strlen($needle));
            $relative = substr($relative, 0, -4); // strip ".php"

            return $namespace . '\\' . str_replace('/', '\\', $relative);
        }

        return null;
    }
}
