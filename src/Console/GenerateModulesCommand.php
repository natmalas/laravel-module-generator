<?php

namespace Nat\ModuleGenerator\Console;

use Illuminate\Console\Command;
use Nat\ModuleGenerator\Generator\GetDirectories;
use Nat\ModuleGenerator\Generator\ModuleGenerator;
use Nat\ModuleGenerator\ScanResult;
use Illuminate\Support\Str;
use Nat\ModuleGenerator\Helper\ModuleGeneratorConfig;
use Nat\ModuleGenerator\UseScanResult;

final class GenerateModulesCommand extends Command
{
    use UseScanResult;

    protected $signature = 'modules:generate';

    protected $description = 'Generate static module facades from attributed classes.';

    public function __construct(
        private ModuleGenerator $generator,
        private GetDirectories $dir,
        private ModuleGeneratorConfig $config
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->_r()->reset();

        $this->info("Generating modules...");

        $this->newLine();

        $directories = $this->dir->get();

        foreach ($directories as $directory) {
            $this->info("Scanning $directory...");
            $this->generator->generate($directory);
            $this->info("Found " . count($this->_r()->byDirectory($directory)) . " classes");
            $this->newLine();
        }

        $this->info("Finished generating modules.");

        $this->newLine();

        $this->info("Total classes: " . count($this->_r()->classes));
        $this->info("Total errors: " . count($this->_r()->errors));

        $this->newLine();

        $this->info("Findings report:");

        foreach ($this->config->roles as $role) {
            $this->info(Str::studly($role->key) . ":" . count($this->_r()->byRole($role)));
        }

        //var_dump(array_filter($this->_r()->errors, fn($e) => $e));

        var_dump($this->_r()->errors[0] ?? []);

        //  var_dump(array_map(fn($class) => $class->directory, $this->_r()->classes));

        return self::SUCCESS;
    }
}
