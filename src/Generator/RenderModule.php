<?php

namespace Nat\ModuleGenerator\Generator;

use Illuminate\Support\Facades\File;
use Nat\ModuleGenerator\DTO\ClassMetadata;
use Illuminate\Support\Str;
use Nat\ModuleGenerator\DTO\ModuleDTO;

class RenderModule
{
    public function module(
        string $namespace,
        string $className,
        string $imports,
        string $methods
    ): string {
        $stub = File::get(__DIR__ . '/../stubs/module.stub');

        return strtr($stub, [
            '{{ namespace }}' => $namespace,
            '{{ module }}' => $className,
            '{{ imports }}' => $imports,
            '{{ methods }}' => $this->indent($methods),
        ]);
    }

    public function indent(string $text, int $levels = 1): string
    {
        $prefix = str_repeat('', $levels);

        return collect(explode("\n", $text))
            ->map(fn(string $line) => $line === '' ? '' : $prefix . $line)
            ->implode("\n");
    }

    public function renderMethod(
        string $methodName,
        string $className
    ): string {
        return sprintf(
            <<<'PHP'
                public static function %s(): %s
                {
                    return app(%s::class);
                }
            PHP,
            $methodName,
            $className,
            $className,
        );
    }
}
