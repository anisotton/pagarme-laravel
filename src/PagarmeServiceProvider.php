<?php

declare(strict_types=1);

namespace Anisotton\Pagarme;

use Anisotton\Pagarme\Commands\PagarmeCommand;
use Anisotton\Pagarme\Contracts\Payments\Charge;
use Anisotton\Pagarme\Contracts\Payments\Item;
use Anisotton\Pagarme\Contracts\Payments\Order;
use Anisotton\Pagarme\Contracts\Wallet\Address;
use Anisotton\Pagarme\Contracts\Wallet\CreditCard;
use Anisotton\Pagarme\Contracts\Wallet\Customer;
use Anisotton\Pagarme\Endpoints\Payload;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class PagarmeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pagarme.php', 'pagarme');

        $this->registerGuzzleClient();
        $this->registerContracts();
        $this->registerEndpoints();
        $this->registerMainService();
    }

    public function boot(): void
    {
        $this->publishConfig();
        $this->registerCommands();
    }

    protected function registerGuzzleClient(): void
    {
        $this->app->singleton(Client::class, function ($app) {
            return new Client([
                'timeout' => config('pagarme.timeout', 30),
                'connect_timeout' => config('pagarme.connect_timeout', 10),
                'verify' => true,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
        });
    }

    protected function registerContracts(): void
    {
        $this->app->singleton(Order::class);
        $this->app->singleton(Charge::class);
        $this->app->singleton(Item::class);
        $this->app->singleton(Address::class);
        $this->app->singleton(Customer::class);
        $this->app->singleton(CreditCard::class);
    }

    protected function registerEndpoints(): void
    {
        $endpoints = [
            Endpoints\Customer::class,
            Endpoints\Recipient::class,
            Endpoints\Charge::class,
            Endpoints\Order::class,
            Endpoints\Subscription::class,
            Endpoints\Plan::class,
            Endpoints\Webhook::class,
        ];

        foreach ($endpoints as $endpoint) {
            $this->app->singleton($endpoint);
        }
    }

    protected function registerMainService(): void
    {
        // Register Payload class
        $this->app->singleton(Payload::class, function ($app) {
            return new Payload(
                $app->make(Order::class),
                $app->make(Charge::class),
                $app->make(Item::class),
                $app->make(Address::class),
                $app->make(Customer::class),
                $app->make(CreditCard::class)
            );
        });

        // Register main Pagarme class
        $this->app->singleton(Pagarme::class, function ($app) {
            return new Pagarme(
                $app->make(Endpoints\Customer::class),
                $app->make(Endpoints\Recipient::class),
                $app->make(Endpoints\Charge::class),
                $app->make(Endpoints\Order::class),
                $app->make(Payload::class),
                $app->make(Endpoints\Subscription::class),
                $app->make(Endpoints\Plan::class),
                $app->make(Endpoints\Webhook::class)
            );
        });
    }

    protected function publishConfig(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/pagarme.php' => config_path('pagarme.php'),
            ], 'pagarme-config');
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PagarmeCommand::class,
            ]);
        }
    }

    public function provides(): array
    {
        return [
            Client::class,
            Order::class,
            Charge::class,
            Item::class,
            Address::class,
            Customer::class,
            CreditCard::class,
            Endpoints\Customer::class,
            Endpoints\Recipient::class,
            Endpoints\Charge::class,
            Endpoints\Order::class,
            Endpoints\Subscription::class,
            Endpoints\Plan::class,
            Endpoints\Webhook::class,
            Payload::class,
            Pagarme::class,
        ];
    }
}
