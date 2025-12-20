<?php

namespace VendorName\Skeleton\Tests;

use GoodMaven\Anvil\Concerns\TestableWorkbench;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use TestableWorkbench;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function getEnvironmentSetUp($app)
    {
        $this->setDatabaseTestingEssentials($app);
    }

    protected function defineDatabaseMigrations(): void
    {
        //
    }
}
