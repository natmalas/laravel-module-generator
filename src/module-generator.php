<?php

return [

    'scan_paths' => [
        app_path(),
    ],

    'exclude_paths' => [],

    'output' => app_path('Modules'),

    'include_mode' => \Nat\ModuleGenerator\Enums\IncludeMode::LOOSE,

    'namespace' => 'App\\Modules',

    'label' => 'Module',

    'structure' => \Nat\ModuleGenerator\Enums\ModuleStructure::GROUPED,

    "nest_modules" => true,

    /**
     * @var list<ModuleRole>
     */
    "roles" => [
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'service',
            directories: ['Services'],
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'job',
            directories: ['Jobs'],
            implements: ["ShouldQueue", "Dispatchable"]
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'controller',
            directories: ['Controllers'],
            extends: ["Controller"]
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'model',
            directories: ['Model'],
            extends: ["Model", "Pivot"]
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'DTO',
            directories: ['DTOs', 'DTO'],
            method: "dtos",
            extends: ["Data"]
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'command',
            directories: ['Commands', 'Console/Commands'],
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'enum',
            directories: ['Enums', 'Enum'],
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'helper',
            directories: ['Helpers'],
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'attribute',
            directories: ['Attributes'],
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'action',
            directories: ['Actions'],
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'repository',
            directories: ['Repositories'],
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'unknown',
            directories: [],
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'rule',
            directories: ['Rules'],
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'trait',
            directories: ['Traits'],
        ),
        new \Nat\ModuleGenerator\DTO\ModuleRole(
            key: 'middleware',
            directories: ['Middleware'],
        ),
    ]
];
