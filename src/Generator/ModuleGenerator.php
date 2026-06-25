<?php

namespace Nat\ModuleGenerator\Generator;

use Illuminate\Support\Facades\File;
use Nat\ModuleGenerator\Config\ModuleGeneratorConfig;
use Nat\ModuleGenerator\DTO\ScanResult;

final class ModuleGenerator
{
    public function __construct(
        private ModuleWriter $writer,
        private ModuleGeneratorConfig $config
    ) {}

    public function generate(): ScanResult
    {
        $result = new ScanResult();

        foreach ($this->config->scanPaths as $scanPath) {
            foreach (File::directories($scanPath) as $directory) {
                try {
                    $result->classes = [
                        ...$result->classes,
                        ...$this->writer->generateModule($directory),
                    ];
                } catch (\Throwable $e) {
                    $result->errors[] = $e;
                }
            }
        }

        return $result;
    }
}
