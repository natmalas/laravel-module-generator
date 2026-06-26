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
    private function _config(string $key): mixed
    {
        return config("module-generator." . $key);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/module-generator.php',
            'module-generator'
        );

        $this->app->singleton(ModuleGeneratorConfig::class, function () {
            return new ModuleGeneratorConfig(
                scanPaths: $this->_config('scan_paths'),
                output: $this->_config('output'),
                includeMode: $this->_config('include_mode'),
                structure: $this->_config('structure'),
                nestModules: $this->_config("nest_modules"),
                roles: $this->_config("roles"),
                label: $this->_config("label")
            );
        });

        $this->app->singleton(
            Parser::class,
            fn() => (new ParserFactory())->createForNewestSupportedVersion()
        );

        $this->app->singleton(
            ScanResult::class
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
