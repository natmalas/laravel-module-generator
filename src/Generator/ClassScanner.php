<?php

namespace Nat\ModuleGenerator\Generator;

use Illuminate\Support\Facades\File;
use Nat\ModuleGenerator\DTO\ClassMetadata;
use Nat\ModuleGenerator\DTO\ClassMetadataProperties;
use Nat\ModuleGenerator\Enums\IncludeMode;
use Nat\ModuleGenerator\DTO\ModuleRole;
use Nat\ModuleGenerator\Helper\ModuleGeneratorConfig;
use Nat\ModuleGenerator\UseScanResult;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_ as NodeClass;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use SplFileInfo;

final class ClassScanner
{
    /**
     * @var list<string>
     */
    private array $errors = [];

    use UseScanResult;

    private Parser $parser;
    private NodeFinder $finder;
    private NodeTraverser $traverser;

    public function __construct(
        private readonly ModuleGeneratorConfig $config,
        private AssignRole $assignRole
    ) {
        $this->parser = (new ParserFactory())->createForNewestSupportedVersion();

        $this->finder = new NodeFinder();

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver());
    }

    /**
     * @return list<ClassMetadata>
     */
    public function getClasses(string $directory): array
    {
        $classes = [];

        foreach (File::allFiles($directory) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            try {
                $metadata = $this->parseFile($file);

                if ($metadata !== null) {
                    $classes[] = $metadata;
                }
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

    private function parseFile(SplFileInfo $file): ?ClassMetadata
    {
        $ast = $this->parser->parse($file->getContents());

        if ($ast === null) {
            return null;
        }

        $ast = $this->traverser->traverse($ast);

        /** @var NodeClass|null $class */
        $class = $this->finder->findFirst(
            $ast,
            fn(Node $node) => $node instanceof NodeClass
        );

        if ($class === null) {
            return null;
        }

        /** @var Node\Name\FullyQualified|null $name */
        $name = $class->namespacedName ?? null;

        if ($name === null || $class->name === null) {
            return null;
        }


        $dto = new ClassMetadata(
            name: $name->toString(),
            shortName: $class->name->toString(),
            namespace: $name->slice(0, -1)->toString(),
            file: $file->getRealPath(),
            startLine: $class->getStartLine(),
            endLine: $class->getEndLine(),

            properties: new ClassMetadataProperties(
                abstract: $class->isAbstract(),
                final: $class->isFinal(),
                readonly: $class->isReadonly(),
                anonymous: $class->isAnonymous(),
            ),

            role: new ModuleRole(key: 'unknown'),

            directory: dirname($file),

            include: $this->shouldInclude($class),

            attributes: $this->resolveAttributes($class),
            constants: $this->resolveConstants($class),
            methods: $this->resolveMethods($class),

            class: $class,

            docComment: $class->getDocComment()?->getText(),
        );

        $dto->role = $this->assignRole->assign($dto);

        return $dto;
    }

    private function resolveAttributes(NodeClass $class): array
    {
        return collect($class->attrGroups)
            ->flatMap(fn($group) => $group->attrs)
            ->all();
    }

    private function resolveConstants(NodeClass $class): array
    {
        return $class->getConstants();
    }

    private function resolveMethods(NodeClass $class): array
    {
        return $class->getMethods();
    }

    private function shouldInclude(NodeClass $class): bool
    {
        if ($class->isAnonymous()) {
            return false;
        }

        return $this->isIncluded($class);
    }

    private function isIncluded(NodeClass $class): bool
    {
        foreach ($class->attrGroups as $group) {
            foreach ($group->attrs as $attribute) {
                $name = $attribute->name->toString();

                if ($name === $this->config->privateClass) {
                    return false;
                }

                if ($name === $this->config->publicClass) {
                    return true;
                }
            }
        }

        return $this->config->includeMode === IncludeMode::LOOSE;
    }
}
