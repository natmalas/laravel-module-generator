<?php

namespace Nat\ModuleGenerator\Attributes;

use Attribute;
use Nat\ModuleGenerator\DTO\ModuleRole;

#[Attribute(Attribute::TARGET_CLASS)]
class Module
{
    public function __construct(
        public ModuleRole|string|null $role = null,
        public ?string $module = null,
        public ?string $className = null,
        public ?string $methodName = null,
        public ?string $description = null,
    ) {}
}
