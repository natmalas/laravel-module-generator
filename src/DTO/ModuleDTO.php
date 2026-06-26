<?php

namespace Nat\ModuleGenerator\DTO;

use Illuminate\Support\Collection;
use Nat\ModuleGenerator\Enums\ModuleStructure;

final readonly class ModuleDTO
{
    public function __construct(
        public string $className,

        public string $baseName,

        public string $directory,

        public string $namespace,

        public string $path,

        public Collection $classes,

        public Collection $modules,

        public ?ModuleRole $role = null,
    ) {}
}
