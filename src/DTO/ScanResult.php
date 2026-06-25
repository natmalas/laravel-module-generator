<?php

namespace Nat\ModuleGenerator\DTO;

class ScanResult
{
    public function __construct(
        public array $classes = [],

        public array $errors = [],
    ) {}
}
