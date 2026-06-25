<?php

namespace Nat\ModuleGenerator\Config;

use Nat\ModuleGenerator\Enums\IncludeMode;

final readonly class ModuleGeneratorConfig
{
    /**
     * @param list<string> $scanPaths
     */
    public function __construct(
        public array $scanPaths,
        public string $output,
        public string $publicClass,
        public string $privateClass,
        public IncludeMode $includeMode,
        public string $namespace,
    ) {}
}

/*final class ModuleGeneratorConfig
{
    private const CONFIG_KEY = "module-generator";

    public const config = [
        "scan_paths" => ""
    ];

    public static function scanPaths(): array
    {
        return config(self::CONFIG_KEY . ".scan_paths");
    }
    public static function output(): string
    {
        return config(self::CONFIG_KEY . ".output");
    }
    public static function publicClass(): string
    {
        return config(self::CONFIG_KEY . ".public_class");
    }
    public static function privateClass(): string
    {
        return config(self::CONFIG_KEY . ".private_class");
    }
    public static function includeMode(): IncludeMode
    {
        return config(self::CONFIG_KEY . ".include_mode");
    }
}
*/