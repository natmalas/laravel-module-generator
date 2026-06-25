<?php

namespace Nat\ModuleGenerator\Generator;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Nat\ModuleGenerator\Config\ModuleGeneratorConfig;
use Nat\ModuleGenerator\Enums\IncludeMode;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;

final class ClassScanner
{
    /**
     * @var list<string>
     */
    protected array $errors = [];

    public function __construct(
        private readonly ModuleGeneratorConfig $config,
        private readonly Parser $parser,
        private readonly NodeFinder $finder,
    ) {}

    public function getClasses(string $directory): array
    {
        $classes = [];

        foreach (File::allFiles($directory) as $file) {

            if ($file->getExtension() !== 'php') {
                continue;
            }

            try {
                $ast = $this->parser->parse($file->getContents());

                if ($ast === null) {
                    continue;
                }

                $traverser = new NodeTraverser();
                $traverser->addVisitor(new NameResolver());

                $ast = $traverser->traverse($ast);

                /** @var Node\Stmt\Class_|null $class */
                $class = $this->finder->findFirstInstanceOf(
                    $ast,
                    Node\Stmt\Class_::class
                );

                if (! $class || $class->isAbstract()) {
                    continue;
                }

                if (! $this->isPublicClass($class)) {
                    continue;
                }

                /** @var Node\Name\FullyQualified|null $resolved */
                $resolved = $class->namespacedName ?? null;

                if ($resolved === null) {
                    continue;
                }

                $classes[] = $resolved->toString();
            } catch (Error $e) {
                $this->errors[] = sprintf(
                    '%s: Parse error: %s',
                    $file->getRelativePathname(),
                    $e->getMessage(),
                );
            } catch (\Throwable $e) {
                $this->errors[] = sprintf(
                    '%s: %s',
                    $file->getRelativePathname(),
                    $e->getMessage(),
                );
            }
        }

        return $classes;
    }

    protected function isPublicClass(Node\Stmt\Class_ $class): bool
    {
        foreach ($class->attrGroups as $group) {
            foreach ($group->attrs as $attribute) {
                // Automatically exclude classes marked private
                if ($attribute->name->toString() === config("module-generator.private_attribute")) {
                    return false;
                }

                // Automatically include classes marked public
                if ($attribute->name->toString() === config("module-generator.public_attribute")) {
                    return true;
                }
            }
        }

        // Unmarked classes = depends on IncludeMode
        if ($this->config->includeMode === IncludeMode::LOOSE) return true;
        return false;
    }
}
