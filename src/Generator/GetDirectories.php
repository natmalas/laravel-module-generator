<?php

namespace Nat\ModuleGenerator\Generator;

use Illuminate\Support\Facades\File;
use Nat\ModuleGenerator\Helper\ModuleGeneratorConfig;

final class GetDirectories
{
    public function __construct(
        private ModuleGeneratorConfig $config,
    ) {}

    public function get(): array
    {
        $directories = [];
        foreach ($this->config->scanPaths as $scanPath) {
            foreach (File::directories($scanPath) as $directory) {
                if ($directory === $this->config->output) continue;
                
                $directories[] = $directory;
            }
        }

        return $directories;
    }
}
