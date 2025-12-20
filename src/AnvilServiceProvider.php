<?php

declare(strict_types=1);

namespace GoodMaven\Anvil;

use GoodMaven\Anvil\Fixes\RegisterLaravelBoosterJsonSchemaFix;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AnvilServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name('anvil');
    }

    public function packageRegistered(): void
    {
        RegisterLaravelBoosterJsonSchemaFix::activate();
    }

    public function packageBooted(): void
    {
        //
    }
}
