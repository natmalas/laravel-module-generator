<?php

namespace Nat\ModuleGenerator\DTO;

final readonly class MethodMetadata
{
    public function __construct(
        public string $name,
        public bool $public,
        public bool $protected,
        public bool $private,
        public bool $static,
        public bool $abstract,
        public bool $final,
        public array $parameters,
        public array $attributes,
        
        public ?string $returnType = null,
    ) {}
}
