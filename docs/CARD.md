# Cartões - Cards

A classe `Card` fornece métodos para gerenciar cartões de crédito, débito e voucher associados aos clientes.

## Métodos Disponíveis

### `create($customerId, $data)`

**Descrição:** Cria um novo cartão associado a um cliente específico.

**Parâmetros:**
- `$customerId` (string): ID do cliente
- `$data` (array): Dados do cartão

**Exemplo:**
```php
$card = Pagarme::card()->create('cus_123', [
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
]);
```

### `find($customerId, $cardId)`

**Descrição:** Obtém as informações de um cartão específico.

**Parâmetros:**
- `$customerId` (string): ID do cliente
- `$cardId` (string): ID do cartão

**Exemplo:**
```php
$card = Pagarme::card()->find('cus_123', 'card_456');
```

### `all($customerId, $queryParams = [])`

**Descrição:** Lista todos os cartões de um cliente.

**Parâmetros:**
- `$customerId` (string): ID do cliente
- `$queryParams` (array): Parâmetros de consulta opcionais

**Exemplo:**
```php
$cards = Pagarme::card()->all('cus_123');
```

### `update($customerId, $cardId, $data)`

**Descrição:** Atualiza as informações de um cartão.

**Parâmetros:**
- `$customerId` (string): ID do cliente
- `$cardId` (string): ID do cartão
- `$data` (array): Novos dados do cartão

**Exemplo:**
```php
$card = Pagarme::card()->update('cus_123', 'card_456', [
    'holder_name' => 'João da Silva',
    'billing_address' => [
        'line_1' => 'Nova Rua, 456',
        'zip_code' => '01310-200',
        'city' => 'São Paulo',
        'state' => 'SP',
        'country' => 'BR'
    ]
]);
```

### `remove($customerId, $cardId)`

**Descrição:** Remove um cartão do cliente.

**Parâmetros:**
- `$customerId` (string): ID do cliente
- `$cardId` (string): ID do cartão

**Exemplo:**
```php
Pagarme::card()->remove('cus_123', 'card_456');
```

### `renew($customerId, $cardId)`

**Descrição:** Renova um cartão existente.

**Parâmetros:**
- `$customerId` (string): ID do cliente
- `$cardId` (string): ID do cartão

**Exemplo:**
```php
$renewedCard = Pagarme::card()->renew('cus_123', 'card_456');
```

### `createToken($data)`

**Descrição:** Cria um token para um cartão. Este endpoint usa autenticação com chave pública configurada no arquivo de configuração.

**Parâmetros:**
- `$data` (array): Dados do cartão para tokenização

**Exemplo:**
```php
$token = Pagarme::card()->createToken([
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
]);
```

### `getBinInfo($bin)`

**Descrição:** Obtém informações sobre o BIN (6 primeiros dígitos) de um cartão.

**Parâmetros:**
- `$bin` (string): BIN do cartão (6 dígitos)

**Exemplo:**
```php
$binInfo = Pagarme::card()->getBinInfo('400000');
```

## Usando DTOs

### CardDto::creditCard()

```php
use Anisotton\Pagarme\DataTransferObjects\CardDto;

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

### CardDto::voucher()

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

### CardDto::privateLabel()

```php
$privateLabelDto = CardDto::privateLabel(
    number: '1234567890123456',
    holderName: 'Carlos Oliveira',
    expMonth: 3,
    expYear: 2027,
    cvv: '789',
    brand: 'store_brand',
    holderDocument: '98765432100'
);

$card = Pagarme::card()->create($customerId, $privateLabelDto->toArray());
```

### CardDto::fromToken()

```php
$cardFromTokenDto = CardDto::fromToken(
    token: 'token_abc123',
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

## Tipos de Cartão Suportados

### Cartão de Crédito
- **Bandeiras:** Visa, Mastercard, American Express, Elo, JCB, Aura, Hipercard, Diners, Discover

### Voucher
- **Bandeiras:** VR, Sodexo, Ticket, Alelo

### Private Label
- Cartões de marca própria da loja

## Considerações Importantes

1. **PCI Compliance:** Para enviar dados de cartão diretamente, você deve ser PCI Compliance. Recomenda-se usar sempre `card_id` ou `card_token`.

2. **Tokenização:** Use a tokenização para maior segurança, especialmente em ambientes frontend.

3. **Billing Address:** É obrigatório para transações de cartão de crédito quando o antifraude estiver habilitado.

4. **Holder Document:** Obrigatório para cartões voucher (VR e Sodexo).

5. **Brand Field:** Obrigatório para cartões Private Label.

## Códigos de Resposta

- **200:** Sucesso
- **404:** Cartão não encontrado
- **412:** Falha na verificação do cartão
- **422:** Dados inválidos
