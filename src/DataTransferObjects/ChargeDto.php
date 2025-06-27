<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\DataTransferObjects;

class ChargeDto extends BaseDto
{
    public function __construct(
        public int $amount,
        public string $currency,
        public string $payment_method,
        public ?string $customer_id = null,
        public ?array $customer = null,
        public ?array $metadata = null,
        public ?array $credit_card = null,
        public ?array $boleto = null,
        public ?array $pix = null,
        public ?string $due_at = null,
        public ?array $antifraud = null
    ) {}

    public static function creditCard(
        int $amount,
        array $creditCard,
        ?string $customerId = null,
        ?array $customer = null,
        ?array $metadata = null
    ): self {
        return new self(
            amount: $amount,
            currency: 'BRL',
            payment_method: 'credit_card',
            customer_id: $customerId,
            customer: $customer,
            metadata: $metadata,
            credit_card: $creditCard
        );
    }

    public static function boleto(
        int $amount,
        ?string $customerId = null,
        ?array $customer = null,
        ?string $dueAt = null,
        ?array $metadata = null
    ): self {
        return new self(
            amount: $amount,
            currency: 'BRL',
            payment_method: 'boleto',
            customer_id: $customerId,
            customer: $customer,
            metadata: $metadata,
            due_at: $dueAt
        );
    }

    public static function pix(
        int $amount,
        ?string $customerId = null,
        ?array $customer = null,
        ?array $metadata = null
    ): self {
        return new self(
            amount: $amount,
            currency: 'BRL',
            payment_method: 'pix',
            customer_id: $customerId,
            customer: $customer,
            metadata: $metadata
        );
    }
}
