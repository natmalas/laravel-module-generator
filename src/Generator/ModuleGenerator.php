<?php

namespace Nat\ModuleGenerator\Generator;

use Nat\ModuleGenerator\UseScanResult;

final class ModuleGenerator
{
    use UseScanResult;

    public function __construct(
        private ModuleWriter $writer,
    ) {}

    public function generate(mixed $directory): void
    {
        try {
            $this->_r()->classes = [
                ...$this->_r()->classes,
                ...$this->writer->generateModule($directory),
            ];
        } catch (\Throwable $e) {
            $this->_r()->errors[] = $e;
        }
    }
}
