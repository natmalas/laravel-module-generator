<?php

namespace Nat\ModuleGenerator\Generator;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Nat\ModuleGenerator\Helper\ModuleGeneratorConfig;

final class ModuleWriter
{
    public function __construct(
        private ClassScanner $scanner,
        private ModuleGeneratorConfig $config
    ) {}

    public function generateModule(string $directory): array
    {
        $classes = $this->scanner->getClasses($directory);

        if ($classes === []) {
            return [];
        }

        $folder = basename($directory);
        $module = "{$folder}Module";

        $imports = collect($classes)
            ->sort()
            ->map(fn($class) => "use {$class};")
            ->implode("\n");

        $methods = [];

        foreach ($classes as $fqcn) {
            $method = Str::camel(class_basename($fqcn));

            if (isset($methods[$method])) {
                throw new \RuntimeException("Duplicate class names detected");
            }

            $methods[$method] = $fqcn;
        }

        $methods = collect($classes)
            ->sort()
            ->map(function (string $fqcn) {

                $short = class_basename($fqcn);

                return sprintf(
                    <<<'PHP'

    public static function %s(): %s
    {
        return app(%s::class);
    }
PHP,
                    Str::camel($short),
                    $short,
                    $short,
                );
            })
            ->implode("\n");

        $contents = <<<PHP
<?php

declare(strict_types=1);

/**
 * @generated
 *
 * DO NOT EDIT.
 * Run:
 *
 * php artisan modules:generate
*/

namespace {$this->config->namespace};

{$imports}

final class {$module}
{
{$methods}
}

PHP;

        File::ensureDirectoryExists($this->config->output);

        File::put(
            "{$this->config->output}/{$module}.php",
            $contents,
        );

        return $classes;
    }
}
