<?php

namespace Nat\ModuleGenerator\Helper;

use Nat\ModuleGenerator\Enums\IncludeMode;
use Nat\ModuleGenerator\Enums\ModuleStructure;

final readonly class ModuleGeneratorConfig
{
    /**
     * @param list<string> $scanPaths
     */
    public function __construct(
        public array $scanPaths,
        public string $output,
        public IncludeMode $includeMode,
        public string $label,
        public ModuleStructure $structure,
        public bool $nestModules,
        public array $roles,
    ) {}
}