<?php

declare(strict_types=1);

namespace GoodMaven\Anvil\Fixes;

use GoodMaven\Anvil\Support\ClassAliasRegistrar;
use Illuminate\JsonSchema\JsonSchema as IlluminateJsonSchema;

class RegisterLaravelBoosterJsonSchemaFix
{
    public static function activate(): void
    {
        ClassAliasRegistrar::register(
            [\Illuminate\Contracts\JsonSchema\JsonSchema::class => IlluminateJsonSchema::class],
            static fn (): bool => static::shouldAliasForBoostMcp(),
        );
    }

    protected static function shouldAliasForBoostMcp(): bool
    {
        if (! app()->runningInConsole()) {
            return false;
        }

        $argv = $_SERVER['argv'] ?? [];

        return in_array('boost:mcp', $argv, true);
    }
}
