<?php

namespace Nat\ModuleGenerator\DTO;

use Nat\ModuleGenerator\Attributes\Module;
use PhpParser\Node\Stmt\Class_;
use Illuminate\Support\Str;

final class ClassMetadata
{
    public function __construct(
        public string $name,

        public string $shortName,

        public string $directory,

        public string $namespace,

        public string $file,

        public int $startLine,

        public Class_ $class,

        public int $endLine,

        public ModuleRole $role,

        public ClassMetadataProperties $properties,

        public array $attributes,

        public array $constants,

        public array $methods,

        public bool $include = false,

        public ?string $docComment = null,

        public ?Module $module = null,
    ) {}

    public function moduleName(): string
    {
        return $this->module?->module ?? basename($this->directory);
    }

    public function className(): string
    {
        return $this->module?->className ?? Str::studly($this->shortName);
    }

    public function methodName(): string
    {
        return $this->module?->methodName
            ?? Str::camel($this->shortName);
    }

    public function role(): ModuleRole
    {
        return $this->module?->role
            ?? $this->role;
    }

    public function description(): ?string
    {
        return $this->module?->description;
    }

    public function include(): bool 
    {
        return $this->include === false || $this->role()->include === false;
    }
}
