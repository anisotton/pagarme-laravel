<?php

namespace Keepcloud\Pagarme;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Keepcloud\Pagarme\Commands\PagarmeCommand;
use Keepcloud\Pagarme\Contracts\Payments\Charge;
use Keepcloud\Pagarme\Contracts\Payments\Item;
use Keepcloud\Pagarme\Contracts\Payments\Order;
use Keepcloud\Pagarme\Contracts\Wallet\Address;
use Keepcloud\Pagarme\Contracts\Wallet\CreditCard;
use Keepcloud\Pagarme\Contracts\Wallet\Customer;
use Keepcloud\Pagarme\Endpoints\Payload;

class PagarmeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pagarme.php', 'pagarme');

        // Register Guzzle HTTP Client
        $this->app->singleton(Client::class, function () {
            return new Client([
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);
        });

        // Register Contract classes
        $this->app->singleton(Order::class);
        $this->app->singleton(Charge::class);
        $this->app->singleton(Item::class);
        $this->app->singleton(Address::class);
        $this->app->singleton(Customer::class);
        $this->app->singleton(CreditCard::class);

        // Register Endpoint classes
        $this->app->singleton(Endpoints\Customer::class);
        $this->app->singleton(Endpoints\Recipient::class);
        $this->app->singleton(Endpoints\Charge::class);
        $this->app->singleton(Endpoints\Order::class);
        $this->app->singleton(Endpoints\Subscription::class);

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
                $app->make(Endpoints\Subscription::class)
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/pagarme.php' => config_path('pagarme.php'),
            ], 'pagarme-config');

            $this->commands([
                PagarmeCommand::class,
            ]);
        }
    }
}
