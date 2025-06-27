# Pagarme Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/anisotton/pagarme-laravel.svg?style=flat-square)](https://packagist.org/packages/anisotton/pagarme-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/anisotton/pagarme-laravel/run-tests?label=tests)](https://github.com/anisotton/pagarme-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/anisotton/pagarme-laravel.svg?style=flat-square)](https://packagist.org/packages/anisotton/pagarme-laravel)

Este pacote é uma integração da API do Pagar.me v5 com o Laravel 12+. O pacote oferece uma interface simples e elegante para trabalhar com a API de pagamentos do Pagar.me.

## Compatibilidade

- **PHP**: ^8.3
- **Laravel**: ^12.0
- **Pagar.me API**: v5

## Instalação

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
```

## Configuração

O arquivo `config/pagarme.php` contém as seguintes configurações:

```php
return [
    'api_key'     => env('PAGARME_API_KEY', 'ak_test_*'),
    'base_url'    => 'https://api.pagar.me/core',
    'api_version' => 'v5',
];
```

## Como usar

### Importando a Facade

```php
use Pagarme;
// ou
use Keepcloud\Pagarme\Facades\Pagarme;
```

### Criando um Cliente

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

### Criando um Pedido

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

### Usando Helpers para Payloads

O pacote oferece helpers para construir payloads de forma mais fácil:

```php
// Criando um item
$item = Pagarme::payload()->item(1000, 'Produto', 'prod-001', 1);

// Criando dados do cliente
$customer = Pagarme::payload()->customer('João Silva', 'joao@example.com', '12345678901');

// Criando endereço
$address = Pagarme::payload()->address(
    'Rua das Flores, 123',
    'Apt 45',
    '01234-567',
    'São Paulo',
    'SP',
    'BR'
);
```

## Endpoints Disponíveis

### Customer (Clientes)
- [Documentação completa](docs/CUSTOMER.md)

### Charge (Cobranças)
- [Documentação completa](docs/CHARGE.md)

### Order (Pedidos)
- [Documentação completa](docs/ORDER.md)

### Recipients (Recebedores)
- [Documentação completa](docs/RECIPIENTS.md)

### Subscription (Assinaturas)
- [Documentação completa](docs/SUBSCRIPTION.md)

## Testando

```bash
composer test
```

## Formatação de Código

```bash
composer format
```

## Changelog

Por favor, consulte o [CHANGELOG](CHANGELOG.md) para mais informações sobre as mudanças recentes.

## Contribuindo

Por favor, consulte [CONTRIBUTING](CONTRIBUTING.md) para detalhes.

## Segurança

Se você descobrir alguma vulnerabilidade de segurança, por favor envie um e-mail para anderson@isotton.com.br.

## Créditos

- [Anderson Isotton](https://github.com/anisotton)
- [Todos os Contribuidores](../../contributors)

## Licença

Licença MIT (MIT). Por favor, consulte o [Arquivo de Licença](LICENSE.md) para mais informações.
