<?php

declare(strict_types=1);

namespace Anisotton\Pagarme;

class Pagarme
{
    public function __construct(
        protected Endpoints\Customer $customer,
        protected Endpoints\Recipient $recipient,
        protected Endpoints\Charge $charge,
        protected Endpoints\Order $order,
        protected Endpoints\Payload $payload,
        protected Endpoints\Subscription $subscription,
        protected Endpoints\Plan $plan,
        protected Endpoints\Webhook $webhook
    ) {}

    public function customer(): Endpoints\Customer
    {
        return $this->customer;
    }

    public function recipient(): Endpoints\Recipient
    {
        return $this->recipient;
    }

    public function charge(): Endpoints\Charge
    {
        return $this->charge;
    }

    public function order(): Endpoints\Order
    {
        return $this->order;
    }

    public function payload(): Endpoints\Payload
    {
        return $this->payload;
    }

    public function subscription(): Endpoints\Subscription
    {
        return $this->subscription;
    }

    public function plan(): Endpoints\Plan
    {
        return $this->plan;
    }

    public function webhook(): Endpoints\Webhook
    {
        return $this->webhook;
    }

    /**
     * Get API version
     */
    public function getApiVersion(): string
    {
        return config('pagarme.api_version', 'v5');
    }

    /**
     * Check if running in sandbox mode
     */
    public function isSandbox(): bool
    {
        return config('pagarme.sandbox', true);
    }

    /**
     * Get base API URL
     */
    public function getBaseUrl(): string
    {
        return config('pagarme.base_url', 'https://api.pagar.me/core');
    }
}
