<?php

return [

    'scan_paths' => [
        app_path(),
    ],

    'output' => app_path('Modules'),

    'public_class' => \Nat\ModuleGenerator\Attributes\IsPublic::class,
    'private_class' => \Nat\ModuleGenerator\Attributes\IsPrivate::class,

    'include_mode' => \Nat\ModuleGenerator\Enums\IncludeMode::LOOSE,

    'namespace' => 'App\\Modules'
];
