# Pagarme Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/anisotton/pagarme-laravel.svg?style=flat-square)](https://packagist.org/packages/anisotton/pagarme-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/anisotton/pagarme-laravel/run-tests?label=tests)](https://github.com/anisotton/pagarme-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/anisotton/pagarme-laravel.svg?style=flat-square)](https://packagist.org/packages/anisotton/pagarme-laravel)

Este pacote é uma integração da API do Pagar.me v5 com o Laravel 12+. O pacote oferece uma interface simples e elegante para trabalhar com a API de pagamentos do Pagar.me, seguindo as melhores práticas do Laravel 12 e PHP 8.3+.

## ✨ Funcionalidades

- ✅ **Integração completa** com a API Pagar.me v5
- ✅ **Type hints** completos para melhor desenvolvimento
- ✅ **Data Transfer Objects (DTOs)** para estruturação de dados
- ✅ **Helpers** para validação e formatação
- ✅ **Logging** configurável de requisições
- ✅ **Tratamento de erros** robusto
- ✅ **Cache** de configurações
- ✅ **Webhooks** com verificação de assinatura
- ✅ **Suporte completo** a PIX, Boleto e Cartão de Crédito

## 🔧 Compatibilidade

- **PHP**: ^8.3
- **Laravel**: ^12.0
- **Pagar.me API**: v5

## 📦 Instalação

Você pode instalar o pacote via Composer:

```bash
composer require anisotton/pagarme-laravel
```

Publique o arquivo de configuração:

```bash
php artisan vendor:publish --tag="pagarme-config"
```

Configure suas credenciais no arquivo `.env`:

```env
PAGARME_API_KEY=ak_live_your_api_key_here
PAGARME_SANDBOX=false
PAGARME_LOG_REQUESTS=true
PAGARME_WEBHOOK_SECRET=your_webhook_secret
```

## ⚙️ Configuração

O arquivo `config/pagarme.php` contém as seguintes configurações:

```php
return [
    // Configurações da API
    'api_key'     => env('PAGARME_API_KEY', 'ak_test_*'),
    'base_url'    => env('PAGARME_BASE_URL', 'https://api.pagar.me/core'),
    'api_version' => env('PAGARME_API_VERSION', 'v5'),
    
    // Configurações de Timeout
    'timeout' => env('PAGARME_TIMEOUT', 30),
    'connect_timeout' => env('PAGARME_CONNECT_TIMEOUT', 10),
    
    // Configurações de Logging
    'log_requests' => env('PAGARME_LOG_REQUESTS', false),
    'log_channel' => env('PAGARME_LOG_CHANNEL', 'default'),
    
    // Ambiente
    'sandbox' => env('PAGARME_SANDBOX', true),
    
    // Webhooks
    'webhook_secret' => env('PAGARME_WEBHOOK_SECRET'),
    'webhook_tolerance' => env('PAGARME_WEBHOOK_TOLERANCE', 300),
    
    // Cache
    'cache_prefix' => env('PAGARME_CACHE_PREFIX', 'pagarme'),
    'cache_ttl' => env('PAGARME_CACHE_TTL', 3600),
];
```

## 🚀 Como usar

### Importando a Facade

```php
use Pagarme;
// ou
use Anisotton\Pagarme\Facades\Pagarme;
```

### 🧑‍💼 Criando um Cliente

#### Usando DTOs (Recomendado)

```php
use Anisotton\Pagarme\DataTransferObjects\CustomerDto;

$customerDto = CustomerDto::individual(
    name: 'João Silva',
    email: 'joao@example.com',
    document: '12345678901',
    phones: [
        'home_phone' => [
            'country_code' => '55',
            'area_code' => '11',
            'number' => '987654321'
        ]
    ]
);

$customer = Pagarme::customer()->create($customerDto->toArray());
```

#### Forma tradicional

```php
$customer = Pagarme::customer()->create([
    'type' => 'individual',
    'name' => 'João Silva',
    'email' => 'joao@example.com',
    'document_type' => 'CPF',
    'document' => '12345678901',
    'phones' => [
        'home_phone' => [
            'country_code' => '55',
            'area_code' => '11',
            'number' => '987654321'
        ]
    ]
]);
```

### 💳 Gerenciando Cartões

#### Criando um Cartão

```php
use Anisotton\Pagarme\DataTransferObjects\CardDto;

// Cartão de crédito usando DTO
$cardDto = CardDto::creditCard(
    number: '4000000000000010',
    holderName: 'João Silva',
    expMonth: 12,
    expYear: 2025,
    cvv: '123',
    holderDocument: '12345678901',
    billingAddress: [
        'line_1' => 'Rua das Flores, 123',
        'zip_code' => '01310-100',
        'city' => 'São Paulo',
        'state' => 'SP',
        'country' => 'BR'
    ]
);

$card = Pagarme::card()->create($customerId, $cardDto->toArray());
```

#### Cartão Voucher

```php
$voucherDto = CardDto::voucher(
    number: '6030000000000000',
    holderName: 'Maria Santos',
    holderDocument: '12345678901',
    expMonth: 6,
    expYear: 2026,
    cvv: '456',
    brand: 'sodexo'
);

$voucher = Pagarme::card()->create($customerId, $voucherDto->toArray());
```

#### Criando Token de Cartão

```php
$tokenData = [
    'type' => 'card',
    'card' => [
        'number' => '4000000000000010',
        'holder_name' => 'João Silva',
        'holder_document' => '12345678901',
        'exp_month' => 12,
        'exp_year' => 2025,
        'cvv' => '123',
        'billing_address' => [
            'line_1' => 'Rua das Flores, 123',
            'zip_code' => '01310-100',
            'city' => 'São Paulo',
            'state' => 'SP',
            'country' => 'BR'
        ]
    ]
];

$token = Pagarme::card()->createToken($tokenData);
```

#### Usando Token para Criar Cartão

```php
$cardFromTokenDto = CardDto::fromToken(
    token: $token['id'],
    billingAddress: [
        'line_1' => 'Av. Paulista, 1000',
        'zip_code' => '01310-100',
        'city' => 'São Paulo',
        'state' => 'SP',
        'country' => 'BR'
    ]
);

$card = Pagarme::card()->create($customerId, $cardFromTokenDto->toArray());
```

#### Outras Operações com Cartões

```php
// Obter cartão
$card = Pagarme::card()->find($customerId, $cardId);

// Listar cartões do cliente
$cards = Pagarme::card()->all($customerId);

// Atualizar cartão
$updatedCard = Pagarme::card()->update($customerId, $cardId, [
    'holder_name' => 'João da Silva'
]);

// Renovar cartão
$renewedCard = Pagarme::card()->renew($customerId, $cardId);

// Remover cartão
Pagarme::card()->remove($customerId, $cardId);

// Obter informações do BIN
$binInfo = Pagarme::card()->getBinInfo('400000');
```

### 💰 Criando Cobranças

#### Cobrança PIX

```php
use Anisotton\Pagarme\DataTransferObjects\ChargeDto;

$chargeDto = ChargeDto::pix(
    amount: 1000, // R$ 10,00 em centavos
    customerId: $customer->id,
    metadata: ['order_id' => '12345']
);

$charge = Pagarme::charge()->createPix($chargeDto->toArray());
```

#### Cobrança com Cartão de Crédito

```php
$chargeDto = ChargeDto::creditCard(
    amount: 1000,
    creditCard: [
        'installments' => 1,
        'statement_descriptor' => 'LOJA',
        'card' => [
            'number' => '4000000000000010',
            'holder_name' => 'João Silva',
            'exp_month' => 12,
            'exp_year' => 2025,
            'cvv' => '123'
        ]
    ],
    customerId: $customer->id
);

$charge = Pagarme::charge()->createCreditCard($chargeDto->toArray());
```

#### Cobrança Boleto

```php
$chargeDto = ChargeDto::boleto(
    amount: 1000,
    customerId: $customer->id,
    dueAt: now()->addDays(3)->toISOString()
);

$charge = Pagarme::charge()->createBoleto($chargeDto->toArray());
```

### 🛒 Criando um Pedido

```php
$order = Pagarme::order()->create([
    'closed' => true,
    'customer_id' => $customer->id,
    'items' => [
        [
            'amount' => 1000, // R$ 10,00 em centavos
            'description' => 'Produto teste',
            'quantity' => 1,
            'code' => 'prod-001'
        ]
    ],
    'payments' => [
        [
            'payment_method' => 'credit_card',
            'credit_card' => [
                'installments' => 1,
                'statement_descriptor' => 'LOJA',
                'card' => [
                    'number' => '4000000000000010',
                    'holder_name' => 'João Silva',
                    'exp_month' => 12,
                    'exp_year' => 2025,
                    'cvv' => '123'
                ]
            ]
        ]
    ]
]);
```

### 📋 Criando Planos

```php
$plan = Pagarme::plan()->create([
    'name' => 'Plano Premium',
    'amount' => 9990, // R$ 99,90
    'currency' => 'BRL',
    'interval' => 'month',
    'interval_count' => 1,
    'billing_type' => 'prepaid',
    'payment_methods' => ['credit_card', 'boleto'],
    'items' => [
        [
            'name' => 'Acesso Premium',
            'quantity' => 1,
            'pricing_scheme' => [
                'price' => 9990
            ]
        ]
    ]
]);
```

### 🔔 Processando Webhooks

```php
use Anisotton\Pagarme\Facades\Pagarme;

Route::post('/webhook/pagarme', function (Request $request) {
    try {
        $signature = $request->header('X-Hub-Signature-256');
        $payload = $request->getContent();
        
        $data = Pagarme::webhook()->processWebhook($payload, $signature);
        
        // Processar o evento do webhook
        switch ($data['type']) {
            case 'charge.paid':
                // Cobrança foi paga
                break;
            case 'charge.failed':
                // Cobrança falhou
                break;
            // ... outros eventos
        }
        
        return response()->json(['status' => 'ok']);
    } catch (\Exception $e) {
        Log::error('Webhook error: ' . $e->getMessage());
        return response()->json(['error' => 'Invalid webhook'], 400);
    }
});
```

### 🛠️ Usando Helpers

```php
use Anisotton\Pagarme\Support\PaymentHelper;

// Converter centavos para reais
$amount = PaymentHelper::centsToCurrency(1000); // 10.00

// Converter reais para centavos
$cents = PaymentHelper::currencyToCents(10.50); // 1050

// Validar CPF
$isValid = PaymentHelper::isValidCpf('12345678901');

// Validar CNPJ
$isValid = PaymentHelper::isValidCnpj('12345678000199');

// Formatar telefone
$phone = PaymentHelper::formatPhone('11987654321');
// Retorna: ['country_code' => '55', 'area_code' => '11', 'number' => '987654321']

// Validar cartão de crédito
$isValid = PaymentHelper::isValidCreditCard('4000000000000010');

// Obter bandeira do cartão
$brand = PaymentHelper::getCreditCardBrand('4000000000000010'); // 'visa'

// Gerar opções de parcelamento
$installments = PaymentHelper::generateInstallments(10000, 12, 2.5);
```

## 📚 Endpoints Disponíveis

### Customer (Clientes)
- `create()` - Criar cliente
- `find($id)` - Obter cliente
- `update($id, $data)` - Atualizar cliente
- `all($queryParams)` - Listar clientes
- `createCreditCard($id, $data)` - Adicionar cartão
- `findCreditCard($id, $cardId)` - Obter cartão
- `allCreditCards($id)` - Listar cartões
- `updateCreditCard($id, $cardId, $data)` - Atualizar cartão
- `deleteCreditCard($id, $cardId)` - Remover cartão
- `createAddress($id, $data)` - Adicionar endereço
- `findAddress($id, $addressId)` - Obter endereço
- `allAddresses($id)` - Listar endereços
- `updateAddress($id, $addressId, $data)` - Atualizar endereço
- `deleteAddress($id, $addressId)` - Remover endereço

### Card (Cartões)
- `create($customerId, $data)` - Criar cartão
- `find($customerId, $cardId)` - Obter cartão
- `all($customerId, $queryParams)` - Listar cartões
- `update($customerId, $cardId, $data)` - Editar cartão
- `remove($customerId, $cardId)` - Excluir cartão
- `renew($customerId, $cardId)` - Renovar cartão
- `createToken($data)` - Criar token do cartão (usa public_key do config)
- `getBinInfo($bin)` - Obter informações do BIN

### Charge (Cobranças)
- `create($data)` - Criar cobrança
- `createPix($data)` - Criar cobrança PIX
- `createBoleto($data)` - Criar cobrança Boleto
- `createCreditCard($data)` - Criar cobrança Cartão
- `find($id)` - Obter cobrança
- `all($queryParams)` - Listar cobranças
- `capture($id, $data)` - Capturar cobrança
- `cancel($id)` - Cancelar cobrança
- `retry($id)` - Reprocessar cobrança
- `editCard($id, $data)` - Editar cartão
- `dueDate($id, $data)` - Alterar vencimento
- `updatePaymentMethod($id, $data)` - Alterar meio de pagamento

### Order (Pedidos)
- `create($data)` - Criar pedido
- `find($id)` - Obter pedido
- `all()` - Listar pedidos
- `close($id)` - Fechar pedido
- `addItem($id, $data)` - Adicionar item
- `updateItem($id, $itemId, $data)` - Atualizar item
- `deleteItem($id, $itemId)` - Remover item
- `deleteAllItems($id)` - Remover todos os itens
- `allItems($id)` - Listar itens

### Plan (Planos)
- `create($data)` - Criar plano
- `find($id)` - Obter plano
- `update($id, $data)` - Atualizar plano
- `deletePlan($id)` - Deletar plano
- `all($queryParams)` - Listar planos
- `addItem($id, $data)` - Adicionar item ao plano
- `updateItem($id, $itemId, $data)` - Atualizar item do plano
- `deleteItem($id, $itemId)` - Remover item do plano
- `getItems($id)` - Listar itens do plano

### Subscription (Assinaturas)
- `create($data)` - Criar assinatura
- `find($id)` - Obter assinatura
- `all($queryParams)` - Listar assinaturas
- `cancel($id)` - Cancelar assinatura
- `updateCard($id, $data)` - Atualizar cartão
- `updateMetadata($id)` - Atualizar metadados
- `updatePaymentMethod($id)` - Atualizar meio de pagamento
- `updateStartAt($id)` - Atualizar data de início
- `updateMinimumPrice($id)` - Atualizar preço mínimo
- `enableManualBilling($id)` - Ativar faturamento manual
- `disableManualBilling($id)` - Desativar faturamento manual

### Recipient (Recebedores)
- `create($data)` - Criar recebedor
- `find($id)` - Obter recebedor
- `update($id, $data)` - Atualizar recebedor
- `all()` - Listar recebedores

### Webhook (Webhooks)
- `create($data)` - Criar webhook
- `find($id)` - Obter webhook
- `update($id, $data)` - Atualizar webhook
- `deleteWebhook($id)` - Deletar webhook
- `all($queryParams)` - Listar webhooks
- `verifySignature($payload, $signature, $secret)` - Verificar assinatura
- `processWebhook($payload, $signature)` - Processar webhook

## 🧪 Testando

```bash
composer test
```

## 🎨 Formatação de Código

```bash
composer format
```

## 📝 Changelog

Por favor, consulte o [CHANGELOG](CHANGELOG.md) para mais informações sobre as mudanças recentes.

## 🤝 Contribuindo

Por favor, consulte [CONTRIBUTING](CONTRIBUTING.md) para detalhes.

## 🔒 Segurança

Se você descobrir alguma vulnerabilidade de segurança, por favor envie um e-mail para anderson@isotton.com.br.

## 👨‍💻 Créditos

- [Anderson Isotton](https://github.com/anisotton)

## 📄 Licença

Este pacote é open-source e licenciado sob a [Licença MIT](LICENSE.md).

---

## 📖 Documentação Adicional

Para documentação mais detalhada sobre cada endpoint, consulte:

- [Customer (Clientes)](docs/CUSTOMER.md)
- [Card (Cartões)](docs/CARD.md)
- [Charge (Cobranças)](docs/CHARGE.md)
- [Order (Pedidos)](docs/ORDER.md)
- [Plan (Planos)](docs/PLAN.md)
- [Subscription (Assinaturas)](docs/SUBSCRIPTION.md)
- [Recipient (Recebedores)](docs/RECIPIENTS.md)
- [Webhook (Webhooks)](docs/WEBHOOK.md)

## 🚀 Roadmap

- [ ] Suporte a marketplace
- [ ] Integração com Laravel Cashier
- [ ] Testes automatizados
- [ ] Cache de respostas
- [ ] Retry automático em falhas
- [ ] Rate limiting
- [ ] Métricas e monitoramento
- [Todos os Contribuidores](../../contributors)

## Licença

Licença MIT (MIT). Por favor, consulte o [Arquivo de Licença](LICENSE.md) para mais informações.
