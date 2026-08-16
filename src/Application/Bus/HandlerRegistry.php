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

        if (null !== $this->cacheFile) {
            file_put_contents(
                $this->cacheFile,
                '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($map, true) . ';' . PHP_EOL,
            );
        }

        return $this->map = $map;
    }

    public function clearCache(): void
    {
        if (null !== $this->cacheFile && is_file($this->cacheFile)) {
            unlink($this->cacheFile);
        }

        $this->map = null;
    }

    /**
     * @return HandlerMap
     */
    private function resolve(): array
    {
        if (null !== $this->map) {
            return $this->map;
        }

        if (null !== $this->cacheFile && is_file($this->cacheFile)) {
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

        if ( ! is_dir($this->sourcePath)) {
            return ['commands' => $commands, 'queries' => $queries];
        }

        foreach (new Finder()->files()->in($this->sourcePath)->name('*.php') as $file) {
            $realPath = $file->getRealPath();

            // Not every .php file under src/ declares a class (routes files,
            // for instance). Guessing wrong and calling class_exists() would
            // still resolve via the PSR-4 autoloader — which requires the
            // file regardless of what's in it, running top-level code like
            // Route::group() outside of any app context.
            if (! $this->declaresClass($realPath)) {
                continue;
            }

            $class = $this->classFromPath($realPath);

            if (null === $class || ! class_exists($class)) {
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

    /**
     * True if the file has a top-level class/interface/enum declaration —
     * checked via tokens rather than a content regex so a "Foo::class"
     * reference elsewhere in the file can't produce a false positive.
     */
    private function declaresClass(string $path): bool
    {
        $tokens = token_get_all((string) file_get_contents($path));

        foreach ($tokens as $i => $token) {
            if (! is_array($token) || ! in_array($token[0], [T_CLASS, T_INTERFACE, T_ENUM], true)) {
                continue;
            }

            $previous = $tokens[$i - 1] ?? null;

            if (is_array($previous) && $previous[0] === T_DOUBLE_COLON) {
                continue; // Foo::class, not a declaration.
            }

            return true;
        }

        return false;
    }

    private function classFromPath(string $realPath): ?string
    {
        $path = str_replace('\\', '/', $realPath);

        foreach (self::NAMESPACE_ROOTS as $namespace => $directory) {
            $needle = '/' . $directory . '/';

            if ( ! str_contains($path, $needle)) {
                continue;
            }

            $relative = mb_substr($path, mb_strpos($path, $needle) + mb_strlen($needle));
            $relative = mb_substr($relative, 0, -4); // strip ".php"

            return $namespace . '\\' . str_replace('/', '\\', $relative);
        }

        return null;
    }
}
