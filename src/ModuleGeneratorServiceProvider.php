<?php

namespace Nat\ModuleGenerator;

use Illuminate\Support\ServiceProvider;
use Nat\ModuleGenerator\Helper\ModuleGeneratorConfig;
use Nat\ModuleGenerator\Console\GenerateModulesCommand;
use Nat\ModuleGenerator\Enums\IncludeMode;
use PhpParser\Parser;
use PhpParser\ParserFactory;

class ModuleGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/module-generator.php',
            'module-generator'
        );

        $this->app->singleton(ModuleGeneratorConfig::class, function () {
            return new ModuleGeneratorConfig(
                scanPaths: config('module-generator.scan_paths'),
                output: config('module-generator.output'),
                publicClass: config('module-generator.public_class'),
                privateClass: config('module-generator.private_class'),
                includeMode: config('module-generator.include_mode'),
                namespace: config("module-generator.namespace"),
            );
        });

        $this->app->singleton(
            Parser::class,
            fn() => (new ParserFactory())->createForNewestSupportedVersion()
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/module-generator.php'
            => config_path('module-generator.php'),
        ], 'module-generator-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateModulesCommand::class,
            ]);
        }
    }
}
