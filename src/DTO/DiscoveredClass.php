<?php

namespace Nat\ModuleGenerator\DTO;

use PhpParser\Node\Stmt\Class_;

final readonly class DiscoveredClass
{
    public function __construct(
        public string $fqcn,
        public string $path,
        public Class_ $node,
    ) {}
}
