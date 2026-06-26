<?php

namespace Nat\ModuleGenerator\Generator;

final class RenderStub
{
    public function renderMethod(
        string $methodName,
        string $className
    ): string {
        return sprintf(
            <<<'PHP'
                public static function %s(): %s
                {
                    return app(%s::class);
                }
            PHP,
            $methodName,
            $className,
            $className,
        );
    }
}
