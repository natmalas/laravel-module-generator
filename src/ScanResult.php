<?php

namespace Nat\ModuleGenerator;

use Nat\ModuleGenerator\DTO\ModuleRole;

class ScanResult
{
    public function __construct(
        public array $classes = [],

        public array $errors = [],

        public array $info = [],
    ) {}

    public function reset(): void
    {
        $this->classes = [];
        $this->errors = [];
        $this->info = [];
    }

    public function byDirectory(string $directory): array
    {
        return array_filter($this->classes, fn($class) => str_contains($class->directory, $directory) || str_contains($directory, $class->directory));
    }
    public function byRole(ModuleRole $role): array
    {
        return array_filter($this->classes, fn($class) => $class->role === $role);
    }
}
