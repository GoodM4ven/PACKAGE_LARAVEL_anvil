<?php

declare(strict_types=1);

namespace GoodMaven\Anvil\Concerns;

use Orchestra\Testbench\Foundation\Config as TestbenchConfig;

trait TestableWorkbench
{
    protected static ?TestbenchConfig $testbenchConfig = null;

    protected function getPackageProviders($app)
    {
        return static::testbench_providers();
    }

    protected function setDatabaseTestingEssentials()
    {
        $this->applyTestbenchEnvironmentVariables();
    }

    protected function databasePathForTesting(): string
    {
        $database = static::testbench_environment()['DB_DATABASE'] ?? 'workbench/database/database.sqlite';

        if ($database === ':memory:') {
            return $database;
        }

        $basePath = $this->resolvePathRelativeToPackageRoot($database);
        $token = $this->parallelTestToken();
        $databaseFile = $token === '' ? basename($basePath) : "database-test-{$token}.sqlite";

        return dirname($basePath).DIRECTORY_SEPARATOR.$databaseFile;
    }

    protected function parallelTestToken(): string
    {
        $token = $_SERVER['TEST_TOKEN'] ?? getenv('TEST_TOKEN');
        $uniqueToken = $_SERVER['UNIQUE_TEST_TOKEN'] ?? getenv('UNIQUE_TEST_TOKEN');

        if (is_string($token) && $token !== '') {
            return $token;
        }

        if (is_string($uniqueToken) && $uniqueToken !== '') {
            return $uniqueToken;
        }

        return '';
    }

    protected static function testbench_configuration(): TestbenchConfig
    {
        return static::$testbenchConfig ??= TestbenchConfig::loadFromYaml(static::packageRootPath());
    }

    protected static function testbench_providers(): array
    {
        return static::testbench_configuration()->getExtraAttributes()['providers'] ?? [];
    }

    protected static function testbench_environment(): array
    {
        return static::testbench_configuration()->getExtraAttributes()['env'] ?? [];
    }

    protected function applyTestbenchEnvironmentVariables(): void
    {
        foreach (static::testbench_environment() as $key => $value) {
            $normalizedValue = match (true) {
                is_bool($value) => $value ? 'true' : 'false',
                default => (string) $value,
            };

            $_ENV[$key] = $normalizedValue;
            $_SERVER[$key] = $normalizedValue;
            putenv("$key=$normalizedValue");
        }
    }

    protected static function packageRootPath(): string
    {
        return dirname(__DIR__);
    }

    protected function resolvePathRelativeToPackageRoot(string $path): string
    {
        if ($this->isAbsolutePath($path)) {
            return $path;
        }

        return static::packageRootPath().DIRECTORY_SEPARATOR.ltrim($path, '/\\');
    }

    protected function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR)
            || preg_match('/^[A-Za-z]:\\\\/', $path) === 1;
    }
}
