<?php

namespace Nat\ModuleGenerator\DTO;

use Illuminate\Support\Str;

final readonly class ModuleRole
{
    public function __construct(
        public string $key,

        public array $directories = [],

        public array $implements = [],

        public array $attributes = [],

        public array $extends = [],

        public ?string $method = null,

        public ?string $className = null,

        public ?string $methodName = null,

        public bool $include = true,
    ) {}

    public static function new(
        string $key,
        array $directories = [],
        array $implements = [],
        array $attributes = [],
        array $extends = [],
        ?string $method = null,
        bool $include = true
    ): static {
        return new static(
            key: $key,
            directories: $directories,
            implements: $implements,
            attributes: $attributes,
            method: $method,
            extends: $extends,
            include: $include,
        );
    }

    public function className(): string 
    {
        return $this->className ?? Str::studly(Str::plural(Str::lower($this->key)));
    }
    public function methodName(): string 
    {
        return $this->methodName ?? Str::camel(Str::plural(Str::lower($this->key)));
    }
}
