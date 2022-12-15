<?php

namespace Hsmfawaz\PaymentGateways;

use Hsmfawaz\PaymentGateways\Commands\PaymentGatewaysCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PaymentGatewaysServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('payment-gateways')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_payment_gateways_table')
            ->hasCommand(PaymentGatewaysCommand::class);
    }
}
