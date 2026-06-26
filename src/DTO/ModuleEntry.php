<?php

namespace Nat\ModuleGenerator\DTO;

final readonly class ModuleEntry
{
    public function __construct(
        public string $className,

        public string $methodName,
    ) {}
}
