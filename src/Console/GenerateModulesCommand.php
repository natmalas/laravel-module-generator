<?php

namespace Nat\ModuleGenerator\Console;

use Illuminate\Console\Command;
use Nat\ModuleGenerator\Generator\ModuleGenerator;

final class GenerateModulesCommand extends Command
{
    protected $signature = 'modules:generate';

    protected $description = 'Generate static module facades from attributed classes.';

    public function __construct(
        private ModuleGenerator $generator
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info("Generating modules...");

        $result = $this->generator->generate();

        $this->info("Classes added: " . implode("\n", $result->classes));
        $this->info("Errors: " . implode(",", $result->errors));

        $this->info("Finished generating modules.");

        $this->info("Total classes: " . count($result->classes));
        $this->info("Total errors: " . count($result->errors));

        return self::SUCCESS;
    }
}
