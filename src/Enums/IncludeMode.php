<?php

namespace Nat\ModuleGenerator\Enums;

enum IncludeMode: string
{
    /**
     * Will include all files that are not marked private
     */
    case LOOSE = 'loose';

    /**
     * Will only include files that are marked public
     */
    case STRICT = 'strict';
}
