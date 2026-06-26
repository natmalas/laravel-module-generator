<?php

namespace Nat\ModuleGenerator\Enums;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Nat\ModuleGenerator\DTO\ClassMetadata;
use PhpParser\Node\Stmt\Class_;

enum ModuleRole: string
{
    case SERVICE = 'service';
    case JOB = 'job';
    case CONTROLLER = 'controller';
    case MODEL = 'model';
    case DTO = 'DTO';
    case COMMAND = 'command';
    case ENUM = 'enum';
    case HELPER = 'helper';
    case ATTRIBUTE = 'attribute';
    case ACTION = 'action';
    case REPOSITORY = 'repository';
    case UNKNOWN = 'unknown';
    case RULE = 'rule';
    case TRAIT = 'trait';
    case MIDDLEWARE = 'middleware';

    public static function dirNames(ModuleRole $role): array
    {
        return match ($role) {
            self::SERVICE => ["Services"],
            self::ATTRIBUTE => ["Attributes"],
            self::RULE => ["Rules"],
            self::TRAIT => ["Traits"],
            self::ACTION => ["Actions"],
            self::HELPER => ["Helpers"],
            self::REPOSITORY => ["Repositories"],
            self::ENUM => ["Enums", "Enum"],
            self::COMMAND => ["Commands", "Console/Commands"],
            self::CONTROLLER => ["Controllers"],
            self::JOB => ["Jobs"],
            self::MODEL => ["Model"],
            self::DTO => ["DTOs", "DTO"],
            self::MIDDLEWARE => ["Middleware"],
            self::UNKNOWN => []
        };
    }
}
