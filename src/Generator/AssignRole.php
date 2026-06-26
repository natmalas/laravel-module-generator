<?php

namespace Nat\ModuleGenerator\Generator;

use Nat\ModuleGenerator\DTO\ClassMetadata;
use Illuminate\Support\Str;
use Nat\ModuleGenerator\DTO\ModuleRole;
use Nat\ModuleGenerator\Helper\ModuleGeneratorConfig;

final class AssignRole
{
    public function __construct(
        private ModuleGeneratorConfig $config
    ) {}

    public function assign(ClassMetadata $dto): ModuleRole
    {
        $roles = $this->config->roles;
        $class = $dto->class;

        // Directories
        $path = str_replace('\\', '/', $dto->name);
        foreach ($roles as $role) {
            /** * @var ModuleRole $role */
            foreach ($role->directories as $dir) {
                if (str_contains($path, '/' . $dir . '/')) return $role;
            }
        }


        // Extends
        $extends = $class->extends?->toString();
        if ($extends) {
            foreach ($roles as $role) {
                /** * @var ModuleRole $role */
                foreach ($role->extends as $extendClass) {
                    if (Str::endsWith($extends, $extendClass)) return $role;
                }
            }
        }

        // Interfaces
        $implements = collect($class->implements)
            ->map(fn($i) => $i->toString());
        foreach ($roles as $role) {
            /** * @var ModuleRole $role */
            foreach ($role->implements as $interface) {
                if ($implements->contains(fn($i) => Str::endsWith($i, $interface))) {
                    return $role;
                }
            }
        }

        // Attributes
        $attributes = collect($class->attrGroups)
            ->flatMap(fn($g) => $g->attrs)
            ->map(fn($a) => $a->name->toString());
        foreach ($roles as $role) {
            /** * @var ModuleRole $role */
            foreach ($role->attributes as $attribute) {
                if ($attributes->contains(fn($a) => Str::endsWith($a, $attribute))) {
                    return $role;
                }
            }
        }

        // Naming conventions
        $name = $class->name?->toString() ?? '';
        foreach ($roles as $role) {
            /** * @var ModuleRole $role */
            if (Str::endsWith($name, $role->key)) return $role;
        }

        return new ModuleRole(
            key: "unknown"
        );
    }
}
