<?php

namespace Nat\ModuleGenerator\Generator;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Nat\ModuleGenerator\DTO\ClassMetadata;
use Nat\ModuleGenerator\Enums\ModuleStructure;
use Nat\ModuleGenerator\Helper\ModuleGeneratorConfig;

final class BuildModule
{
    public function __construct(
        private ModuleGeneratorConfig $config,
        private RenderStub $render
    ) {}

    public function buildImports(Collection $classes): string
    {
        return $classes
            ->map(fn(ClassMetadata $class) => "use {$class->name};")
            ->implode("\n");
    }

    private function buildRoleImports(
        string $module,
        Collection $grouped,
    ): string {
        return $grouped
            ->keys()
            ->map(fn(string $role) => sprintf(
                'use %s\%s;',
                $this->moduleNamespace($module),
                $this->roleModuleName($module, $role),
            ))
            ->implode("\n");
    }

    private function buildFlatMethods(Collection $classes): string
    {
        return $classes
            ->map(fn(ClassMetadata $class) => $this->render->renderMethod(
                $class->methodName(),
                $class->className()
            ))
            ->implode("\n");
    }

    private function buildRoleMethods(
        string $module,
        Collection $grouped,
    ): string {
        return $grouped
            ->keys()
            ->map(function (string $role) use ($module) {
                $method = $role->method ?? Str::camel(Str::plural($role));
                $class = $this->roleModuleName($module, $role);

                return <<<PHP
                    public static function {$method}(): {$class}
                    {
                        return new {$class}();
                    }
                PHP;
            })
            ->implode("\n\n");
    }

    private function roleModuleName(
        string $module,
        string $role,
    ): string {
        return sprintf(
            '%s%sModule',
            Str::beforeLast($module, 'Module'),
            Str::studly($role),
        );
    }

    private function moduleNamespace(string $module): string
    {
        if (! $this->config->nestModules) {
            return $this->config->namespace;
        }

        $directory = Str::beforeLast($module, 'Module');

        foreach (
            array_map(
                fn($role) => Str::studly($role->key),
                $this->config->roles,
            ) as $role
        ) {
            if (Str::endsWith($directory, $role)) {
                $directory = Str::beforeLast($directory, $role);
                break;
            }
        }

        return "{$this->config->namespace}\\{$directory}";
    }
}
