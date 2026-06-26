<?php

namespace Nat\ModuleGenerator;

use Nat\ModuleGenerator\ScanResult;

trait UseScanResult
{
    protected function _r(): ScanResult
    {
        return app(ScanResult::class);
    }
}
