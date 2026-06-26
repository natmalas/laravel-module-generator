<?php

namespace Nat\ModuleGenerator\Generator;

use Illuminate\Support\Collection;
use Nat\ModuleGenerator\DTO\ClassMetadata;
use RuntimeException;
use Illuminate\Support\Str;

final class DiscoverModules
{
    public function __construct(
        private ClassScanner $scanner
    ) {}

    public function scanClasses(string $directory): Collection
    {
        $scanned = $this->scanner->getClasses($directory);

        return collect($scanned)
            // filter by role  
            ->filter(fn(ClassMetadata $class) => $class->role->include === true)
            // sort by name
            ->sortBy(fn(ClassMetadata $class) => $class->name)
            ->values();
    }

    public function validateDuplicateMethods(Collection $classes): void
    {
        $methods = [];

        foreach ($classes as $class) {
            /** @var ClassMetadata $class */
            $method = $this->exportedMethod($class);

            if (isset($methods[$method])) {
                throw new RuntimeException(
                    "Duplicate generated method '{$method}'."
                );
            }

            $methods[$method] = true;
        }
    }

    private function exportedMethod(ClassMetadata $class): string
    {
        return $class->module?->exportAs
            ?? Str::camel($class->shortName);
    }
}
