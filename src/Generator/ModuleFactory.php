<?php

namespace Nat\ModuleGenerator\Generator;

use Illuminate\Support\Collection;
use Nat\ModuleGenerator\DTO\ModuleDTO;
use Nat\ModuleGenerator\Helper\ModuleGeneratorConfig;
use Illuminate\Support\Str;
use Nat\ModuleGenerator\Attributes\Module;
use Nat\ModuleGenerator\DTO\ModuleRole;
use Nat\ModuleGenerator\Enums\ModuleStructure;

final class ModuleFactory
{
    public function __construct(
        private ModuleGeneratorConfig $config
    ) {}

    public function createModule(
        string $directory,
        ?ModuleRole $role = null,
        ?Collection $classes = null,
        ?Module $attribute = null,
    ): ModuleDTO {
        $baseName =  basename($directory);
        $directory = $baseName;

        $className = $baseName . $this->config->label . $role?->className();
        if ($attribute?->className) $className = $attribute->className;

        if ($role !== null) {
            $directory = Str::beforeLast(
                $directory,
                Str::studly($role->key),
            );
        }

        $classes = $classes ?? new Collection();
        $modules = $modules ?? new Collection();

        $structure = ModuleStructure::FLAT;
        if ($role && $this->config->structure === ModuleStructure::GROUPED) $structure = ModuleStructure::GROUPED;

        if (! $this->config->nestModules) {
            return new ModuleDTO(
                className: $className,
                directory: $directory,
                baseName: $baseName,
                classes: $classes,
                //structure: $structure,
                modules: $modules,
                namespace: $this->namespaceForDirectory($this->config->output),
                path: "{$this->config->output}/{$className}.php",
                role: $role,
            );
        }

        return new ModuleDTO(
            className: $className,
            directory: $directory,
            baseName: $baseName,
            classes: $classes,
            //structure: $structure,
            modules: $modules,
            namespace: "{$this->namespaceForDirectory($this->config->output)}\\{$directory}",
            path: "{$this->config->output}/{$directory}/{$className}.php",
            role: $role,
        );
    }

    private function namespaceForDirectory(string $directory): string
    {
        $appNamespace = app()->getNamespace();

        $relative = Str::after($directory, app_path());

        $relative = trim($relative, DIRECTORY_SEPARATOR);

        if ($relative === '') {
            return rtrim($appNamespace, '\\');
        }

        return rtrim($appNamespace, '\\')
            . '\\'
            . str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
    }
}
