<?php

namespace Keepcloud\Pagarme\Tests;

use Keepcloud\Pagarme\PagarmeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            PagarmeServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('pagarme.api_key', 'ak_test_123456789');
        config()->set('pagarme.base_url', 'https://api.pagar.me/core');
        config()->set('pagarme.api_version', 'v5');
    }
}
