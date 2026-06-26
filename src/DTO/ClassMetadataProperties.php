<?php

namespace Nat\ModuleGenerator\DTO;

final readonly class ClassMetadataProperties
{
    public function __construct(
        public bool $abstract,

        public bool $final,

        public bool $readonly,

        public bool $anonymous,
    ) {}
}
