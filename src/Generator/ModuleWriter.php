<?php

namespace Nat\ModuleGenerator\Generator;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Nat\ModuleGenerator\DTO\ClassMetadata;
use Nat\ModuleGenerator\DTO\ModuleDTO;
use Nat\ModuleGenerator\DTO\ModuleRole;
use Nat\ModuleGenerator\Enums\ModuleStructure;
use Nat\ModuleGenerator\Helper\ModuleGeneratorConfig;
use RuntimeException;

final readonly class ModuleWriter
{
    public function __construct(
        private ClassScanner $scanner,
        private ModuleGeneratorConfig $config,
        private RenderModule $render,
        private ModuleFactory $factory
    ) {}

    /**
     * @return list<ClassMetadata>
     */
    public function generateModule(string $directory): array
    {
        $classes = $this->scanClasses($directory);

        if ($classes->isEmpty()) {
            return [];
        }

        // Validation
        $this->validateDuplicateMethods($classes);

        // Create dir if not exists
        File::ensureDirectoryExists($this->config->output);

        if ($this->config->structure === ModuleStructure::FLAT) {
            $module = $this->factory->createModule($directory, null, $classes);

            $methods = $this->buildMethods($module);
            $imports = $this->buildImports($classes);
            $this->writeModule(
                $module,
                $imports,
                $methods
            );

            return $classes->all();
        }

        $modules = $this->buildModules(
            $directory,
            $classes
        );

        $writtenModules = [];
        foreach ($modules as $module) {
            /** @var ModuleDTO $module */
            $classes = $module->classes;

            $methods =  $this->buildMethods($module);
            if (!$methods) continue;

            $imports = $this->buildImports($classes);

            $this->writeModule(
                $module,
                $imports,
                $methods
            );
            $writtenModules[] = $module;
        }

        $this->writeMainModule(
            $writtenModules,
            $directory
        );

        return $classes->all();
    }

    /**
     * @return Collection<int, ClassMetadata>
     */
    private function scanClasses(string $directory): Collection
    {
        return collect($this->scanner->getClasses($directory))
            ->filter(fn(ClassMetadata $class) => $class->role->include === true)
            ->sortBy(fn(ClassMetadata $class) => $class->name)
            ->values();
    }

    private function validateDuplicateMethods(Collection $classes): void
    {
        $methods = [];

        foreach ($classes as $class) {
            /** @var ClassMetadata $class */
            $method = $class->methodName();

            if (isset($methods[$method])) {
                throw new RuntimeException(
                    "Duplicate generated method '{$method}'."
                );
            }

            $methods[$method] = true;
        }
    }

    private function writeMainModule(
        array $modules,
        string $directory
    ): void {
        $imports = "";
        $methods = "";

        foreach ($modules as $module) {
            /** @var ModuleDTO $module */
            $methods .= $this->render->renderMethod(
                $module->role->methodName(),
                $module->className,
            ) . "\n";
            $imports .= sprintf(
                "use %s\%s; \n",
                $module->namespace,
                $module->className,
            );
        }

        $module = $this->factory->createModule(
            $directory,
        );

        $this->writeModule(
            $module,
            $imports,
            $methods
        );
    }

    private function buildMethods(ModuleDTO $module): string
    {
        return $module->classes
            ->map(fn(ClassMetadata $class) => $this->render->renderMethod(
                $class->methodName(),
                $class->className()
            ))
            ->implode("\n");
    }

    private function buildModules(
        string $directory,
        Collection $classes
    ): array {
        $modules = [];

        foreach ($this->config->roles as $role) {
            $modules[] = $this->factory->createModule(
                $directory,
                $role,
                $classes->filter(fn($class) => $class->role()->key === $role->key)
            );
        }
        return $modules;
    }

    private function buildImports(Collection $classes): string
    {
        return $classes
            ->map(fn(ClassMetadata $class) => "use {$class->name};")
            ->implode("\n");
    }

    private function writeModule(
        ModuleDTO $module,
        string $imports,
        string $methods,
    ): void {
        File::ensureDirectoryExists(dirname($module->path));

        File::put(
            $module->path,
            $this->render->module(
                namespace: $module->namespace,
                className: $module->className,
                imports: $imports,
                methods: $methods,
            ),
        );
    }
}
