<?php

/**
 * Exemplo completo de uso do Pagarme Laravel Package
 *
 * Este arquivo demonstra as principais funcionalidades do pacote
 * seguindo as melhores práticas do Laravel 12 e PHP 8.3+
 */

use Anisotton\Pagarme\Facades\Pagarme;
use Anisotton\Pagarme\DataTransferObjects\CustomerDto;
use Anisotton\Pagarme\DataTransferObjects\ChargeDto;
use Anisotton\Pagarme\Support\PaymentHelper;

class PagarmeExampleUsage
{
    /**
     * Exemplo de criação de cliente usando DTOs
     */
    public function createCustomerExample(): array
    {
        // Criando cliente pessoa física usando DTO
        $customerDto = CustomerDto::individual(
            name: 'João Silva',
            email: 'joao@example.com',
            document: '11144477735', // CPF válido para teste
            phones: [
                'home_phone' => PaymentHelper::formatPhone('11987654321')
            ],
            address: [
                'line_1' => 'Rua das Flores, 123',
                'line_2' => 'Apt 45',
                'zip_code' => '01234567',
                'city' => 'São Paulo',
                'state' => 'SP',
                'country' => 'BR'
            ],
            metadata: [
                'source' => 'website',
                'campaign' => 'summer_2025'
            ]
        );

        $response = Pagarme::customer()->create($customerDto->toArray());
        $customer = json_decode($response->getBody()->getContents(), true);

        return $customer;
    }

    /**
     * Exemplo de criação de cobrança PIX
     */
    public function createPixChargeExample(string $customerId): array
    {
        $chargeDto = ChargeDto::pix(
            amount: PaymentHelper::currencyToCents(49.90), // R$ 49,90
            customerId: $customerId,
            metadata: [
                'order_id' => 'ORDER-12345',
                'product' => 'Produto Premium'
            ]
        );

        $response = Pagarme::charge()->createPix($chargeDto->toArray());
        $charge = json_decode($response->getBody()->getContents(), true);

        return $charge;
    }

    /**
     * Exemplo de criação de cobrança com cartão de crédito
     */
    public function createCreditCardChargeExample(string $customerId): array
    {
        // Validar cartão antes de processar
        $cardNumber = '4111111111111111';

        if (!PaymentHelper::isValidCreditCard($cardNumber)) {
            throw new \InvalidArgumentException('Cartão de crédito inválido');
        }

        $brand = PaymentHelper::getCreditCardBrand($cardNumber);

        $chargeDto = ChargeDto::creditCard(
            amount: PaymentHelper::currencyToCents(199.90), // R$ 199,90
            creditCard: [
                'installments' => 3,
                'statement_descriptor' => 'MINHA LOJA',
                'card' => [
                    'number' => $cardNumber,
                    'holder_name' => 'JOAO SILVA',
                    'exp_month' => 12,
                    'exp_year' => 2025,
                    'cvv' => '123',
                    'brand' => $brand
                ]
            ],
            customerId: $customerId,
            metadata: [
                'order_id' => 'ORDER-12346',
                'installments_info' => PaymentHelper::generateInstallments(
                    PaymentHelper::currencyToCents(199.90),
                    3,
                    2.5 // 2.5% de juros ao mês
                )
            ]
        );

        $response = Pagarme::charge()->createCreditCard($chargeDto->toArray());
        $charge = json_decode($response->getBody()->getContents(), true);

        return $charge;
    }

    /**
     * Exemplo de criação de cobrança boleto
     */
    public function createBoletoChargeExample(string $customerId): array
    {
        $dueDate = now()->addDays(3)->toISOString();

        $chargeDto = ChargeDto::boleto(
            amount: PaymentHelper::currencyToCents(89.90), // R$ 89,90
            customerId: $customerId,
            dueAt: $dueDate,
            metadata: [
                'order_id' => 'ORDER-12347',
                'due_date_human' => now()->addDays(3)->format('d/m/Y')
            ]
        );

        $response = Pagarme::charge()->createBoleto($chargeDto->toArray());
        $charge = json_decode($response->getBody()->getContents(), true);

        return $charge;
    }

    /**
     * Exemplo de criação de plano de assinatura
     */
    public function createSubscriptionPlanExample(): array
    {
        $planData = [
            'name' => 'Plano Premium Mensal',
            'amount' => PaymentHelper::currencyToCents(99.90),
            'currency' => 'BRL',
            'interval' => 'month',
            'interval_count' => 1,
            'billing_type' => 'prepaid',
            'payment_methods' => ['credit_card', 'boleto'],
            'minimum_price' => PaymentHelper::currencyToCents(99.90),
            'installments' => [1, 2, 3],
            'items' => [
                [
                    'name' => 'Acesso Premium',
                    'quantity' => 1,
                    'pricing_scheme' => [
                        'price' => PaymentHelper::currencyToCents(99.90)
                    ]
                ]
            ],
            'metadata' => [
                'category' => 'premium',
                'features' => 'unlimited_access,priority_support'
            ]
        ];

        $response = Pagarme::plan()->create($planData);
        $plan = json_decode($response->getBody()->getContents(), true);

        return $plan;
    }

    /**
     * Exemplo de criação de assinatura
     */
    public function createSubscriptionExample(string $customerId, string $planId): array
    {
        $subscriptionData = [
            'customer_id' => $customerId,
            'plan_id' => $planId,
            'payment_method' => 'credit_card',
            'card' => [
                'number' => '4111111111111111',
                'holder_name' => 'JOAO SILVA',
                'exp_month' => 12,
                'exp_year' => 2025,
                'cvv' => '123'
            ],
            'start_at' => now()->addDays(1)->toISOString(),
            'metadata' => [
                'source' => 'website',
                'promo_code' => 'WELCOME2025'
            ]
        ];

        $response = Pagarme::subscription()->create($subscriptionData);
        $subscription = json_decode($response->getBody()->getContents(), true);

        return $subscription;
    }

    /**
     * Exemplo de processamento de webhook
     */
    public function processWebhookExample(string $payload, string $signature): array
    {
        try {
            $data = Pagarme::webhook()->processWebhook($payload, $signature);

            // Log do evento recebido
            \Log::info('Pagarme Webhook Received', [
                'type' => $data['type'] ?? 'unknown',
                'id' => $data['id'] ?? null,
                'timestamp' => now()->toISOString()
            ]);

            // Processar diferentes tipos de eventos
            switch ($data['type']) {
                case 'charge.paid':
                    return $this->handleChargePaid($data);

                case 'charge.failed':
                    return $this->handleChargeFailed($data);

                case 'charge.refunded':
                    return $this->handleChargeRefunded($data);

                case 'subscription.created':
                    return $this->handleSubscriptionCreated($data);

                case 'subscription.canceled':
                    return $this->handleSubscriptionCanceled($data);

                default:
                    \Log::warning('Unhandled webhook type', ['type' => $data['type']]);
                    return ['status' => 'ignored'];
            }

        } catch (\Exception $e) {
            \Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'payload_preview' => substr($payload, 0, 100)
            ]);

            throw $e;
        }
    }

    /**
     * Processar evento de cobrança paga
     */
    private function handleChargePaid(array $data): array
    {
        $chargeId = $data['data']['id'];
        $amount = PaymentHelper::centsToCurrency($data['data']['amount']);

        // Aqui você implementaria sua lógica de negócio
        // Por exemplo: atualizar status do pedido, enviar email, etc.

        \Log::info('Charge paid successfully', [
            'charge_id' => $chargeId,
            'amount' => $amount
        ]);

        return ['status' => 'processed', 'action' => 'charge_paid'];
    }

    /**
     * Processar evento de cobrança falhada
     */
    private function handleChargeFailed(array $data): array
    {
        $chargeId = $data['data']['id'];
        $reason = $data['data']['last_transaction']['acquirer_return_code'] ?? 'unknown';

        \Log::warning('Charge failed', [
            'charge_id' => $chargeId,
            'reason' => $reason
        ]);

        return ['status' => 'processed', 'action' => 'charge_failed'];
    }

    /**
     * Processar evento de estorno
     */
    private function handleChargeRefunded(array $data): array
    {
        $chargeId = $data['data']['id'];
        $refundAmount = PaymentHelper::centsToCurrency($data['data']['amount_refunded']);

        \Log::info('Charge refunded', [
            'charge_id' => $chargeId,
            'refund_amount' => $refundAmount
        ]);

        return ['status' => 'processed', 'action' => 'charge_refunded'];
    }

    /**
     * Processar evento de assinatura criada
     */
    private function handleSubscriptionCreated(array $data): array
    {
        $subscriptionId = $data['data']['id'];

        \Log::info('Subscription created', [
            'subscription_id' => $subscriptionId
        ]);

        return ['status' => 'processed', 'action' => 'subscription_created'];
    }

    /**
     * Processar evento de assinatura cancelada
     */
    private function handleSubscriptionCanceled(array $data): array
    {
        $subscriptionId = $data['data']['id'];

        \Log::info('Subscription canceled', [
            'subscription_id' => $subscriptionId
        ]);

        return ['status' => 'processed', 'action' => 'subscription_canceled'];
    }

    /**
     * Exemplo completo de uso em uma aplicação
     */
    public function completeExampleFlow(): array
    {
        try {
            // 1. Criar cliente
            $customer = $this->createCustomerExample();

            // 2. Criar diferentes tipos de cobrança
            $pixCharge = $this->createPixChargeExample($customer['id']);
            $creditCardCharge = $this->createCreditCardChargeExample($customer['id']);
            $boletoCharge = $this->createBoletoChargeExample($customer['id']);

            // 3. Criar plano e assinatura
            $plan = $this->createSubscriptionPlanExample();
            $subscription = $this->createSubscriptionExample($customer['id'], $plan['id']);

            return [
                'success' => true,
                'customer' => $customer,
                'charges' => [
                    'pix' => $pixCharge,
                    'credit_card' => $creditCardCharge,
                    'boleto' => $boletoCharge
                ],
                'subscription' => [
                    'plan' => $plan,
                    'subscription' => $subscription
                ]
            ];

        } catch (\Exception $e) {
            \Log::error('Complete flow error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
