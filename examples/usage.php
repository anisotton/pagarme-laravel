<?php

declare(strict_types=1);

/**
 * Exemplo de uso do Pagarme Laravel Package
 *
 * Este arquivo demonstra como usar o pacote em uma aplicação Laravel
 */

use Anisotton\Pagarme\Facades\Pagarme;

// 1. Criando um cliente
$customerData = [
    'type' => 'individual',
    'name' => 'João Silva',
    'email' => 'joao@example.com',
    'document_type' => 'CPF',
    'document' => '12345678901',
    'phones' => [
        'home_phone' => [
            'country_code' => '55',
            'area_code' => '11',
            'number' => '987654321',
        ],
    ],
    'address' => [
        'line_1' => 'Rua das Flores, 123',
        'line_2' => 'Apt 45',
        'zip_code' => '01234567',
        'city' => 'São Paulo',
        'state' => 'SP',
        'country' => 'BR',
    ],
];

// Criar cliente
$customer = Pagarme::customer()->create($customerData);

// 2. Usando helpers para criar payloads
$item = Pagarme::payload()->item(
    amount: 2000, // R$ 20,00 em centavos
    description: 'Produto Exemplo',
    code: 'prod-001',
    quantity: 1
);

$customerPayload = Pagarme::payload()->customer(
    name: 'Maria Silva',
    email: 'maria@example.com',
    document: '98765432101'
);

$address = Pagarme::payload()->address(
    lineOne: 'Av. Paulista, 1000',
    lineTwo: 'Sala 101',
    zipCode: '01310-100',
    city: 'São Paulo',
    state: 'SP',
    country: 'BR'
);

// 3. Criando um pedido com cartão de crédito
$orderData = [
    'closed' => true,
    'customer_id' => $customer['id'], // ID do cliente criado anteriormente
    'items' => [$item],
    'payments' => [
        [
            'payment_method' => 'credit_card',
            'credit_card' => [
                'installments' => 1,
                'statement_descriptor' => 'MINHA LOJA',
                'card' => [
                    'number' => '4000000000000010', // Cartão de teste
                    'holder_name' => 'João Silva',
                    'exp_month' => 12,
                    'exp_year' => 2025,
                    'cvv' => '123',
                    'billing_address' => $address,
                ],
            ],
        ],
    ],
];

// Criar pedido
$order = Pagarme::order()->create($orderData);

// 4. Listando clientes
$customers = Pagarme::customer()->all();

// 5. Buscando um cliente específico
$customer = Pagarme::customer()->find($customerId);

// 6. Criando uma cobrança avulsa
$chargeData = [
    'amount' => 1500, // R$ 15,00
    'customer_id' => $customerId,
    'payment_method' => 'credit_card',
    'credit_card' => [
        'installments' => 1,
        'statement_descriptor' => 'COBRANCA',
        'card_id' => $cardId, // ID de um cartão já salvo
    ],
];

$charge = Pagarme::charge()->create($chargeData);

// 7. Capturando uma cobrança
$capture = Pagarme::charge()->capture($chargeId, [
    'amount' => 1500, // Valor a ser capturado (opcional)
]);

// 8. Criando uma assinatura
$subscriptionData = [
    'customer_id' => $customerId,
    'plan_id' => $planId,
    'payment_method' => 'credit_card',
    'card_id' => $cardId,
];

$subscription = Pagarme::subscription()->create($subscriptionData);

echo "✅ Exemplos executados com sucesso!\n";
echo "📋 Cliente criado: {$customer['name']}\n";
echo "🛒 Pedido criado: {$order['id']}\n";
echo "💳 Cobrança criada: {$charge['id']}\n";
