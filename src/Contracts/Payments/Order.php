<?php

namespace Keepcloud\Pagarme\Contracts\Payments;

use Exception;

final readonly class Order
{
    // NÃO ENVIE DADOS ABERTOS DO CARTÃO DO COMPRADOR -> Para poder trafegar dados de cartão abertos em seu servidor, você deve ser PCI Compliance. Por isso, recomendamos fortemente que as requisições sejam enviadas sempre usando o card_id ou card_token, de forma que você não precise trafegar os dados de cartão no seu servidor.
    // DADOS DO COMPRADOR SÃO OBRIGATÓRIOS -> Caso seja informado o customer_id, não é necessário incluir o objeto customer. Entretanto, é obrigatório que um desses parâmetros seja informado.
    // O campo billing é obrigatório para todas as transações de cartão de crédito quando o antifraude estiver habilitado.
    public const CREATE_ORDER = [
        'closed' => 'boolean',
        'code' => 'string',
        'customer_id' => 'string',
        'customer' => 'array',
        'items' => 'array',
        'shipping' => 'array',
        'payments' => 'array',
        'metadata' => 'array',
        'device' => 'array', // Normalmente é utilizado para integração com Antifraude externo
        'location' => 'array', // Normalmente é utilizado para integração com Antifraude externo
        'ip' => 'ip', // Normalmente é utilizado para integração com Antifraude externo
        'session_id' => 'string', // Normalmente é utilizado para integração com Antifraude externo
    ];

    public const ORDER_PAYMENT_CREDIT_CARD = [
        'payment_method' => 'string',
        'credit_card.installments' => 'integer',
        'credit_card.statement_descriptor' => 'string|max:13',
        'credit_card.card.number' => 'string',
        'credit_card.card.holder_name' => 'string',
        'credit_card.card.holder_document' => 'string',
        'credit_card.card.exp_month' => 'integer|min:1|max:12',
        'credit_card.card.exp_year' => 'date_format:Y|after_or_equal:now',
        'credit_card.card.cvv' => 'string|min:3|max:4',
        'credit_card.card.billing_address.line_1' => 'string',
        'credit_card.card.billing_address.zip_code' => 'string',
        'credit_card.card.billing_address.city' => 'string',
        'credit_card.card.billing_address.state' => 'string',
        'credit_card.card.billing_address.country' => 'string',
    ];

    // A confirmação do CVV não é obrigatória, mas é importante pois funciona como uma camada de segurança. Por isso, indicamos sempre solicitar a confirmação dessa informação.
    // O campo billing é obrigatório para todas as transações de cartão de crédito quando o antifraude estiver habilitado.
    public const ORDER_PAYMENT_CREDIT_CARD_ID_CVV = [
        'payment_method' => 'string',
        'credit_card.operation_type' => 'string',
        'credit_card.installments' => 'integer',
        'credit_card.statement_descriptor' => 'string|max:13',
        'credit_card.card_id' => 'string',
        'credit_card.cvv' => 'string|min:3|max:4',
        'credit_card.billing_address' => 'array',
    ];

    public const ORDER_PAYMENT_CREDIT_CARD_TOKEN = [
        'payment_method' => 'string',
        'credit_card.operation_type' => 'string',
        'credit_card.installments' => 'integer',
        'credit_card.statement_descriptor' => 'string|max:13',
        'credit_card.card_token' => 'string',
        'credit_card.card.billing_address' => 'array',
    ];

    // Para testes com boleto, na sua dashboard vá em Configurações > meios de pagamento > boleto > modelo de negócio > Selecione "Simulator"
    // DADOS DO COMPRADOR SÃO OBRIGATÓRIOS -> Caso seja informado o customer_id, não é necessário incluir o objeto customer. Entretanto, é obrigatório que um desses parâmetros seja informado.
    public const ORDER_PAYMENT_BANK_SLIP = [
        'payment_method' => 'string',
        'boleto.instructions' => 'string',
        'boleto.due_at' => 'date_format:Y-m-d\TH:i:s\Z',
    ];

    // Valor limite para multa: 1° As multas de mora decorrentes do inadimplemento de obrigações no seu termo não poderão ser superiores a dois por cento do valor da prestação.
    // Valor limite para Juros: Segundo o art. 406 do Código Civil e o artigo 161, parágrafo primeiro, do Código Tributário Nacional, os juros de mora devem ser cobrados a, no máximo, 1% ao mês.
    public const ORDER_PAYMENT_BANK_SLIP_COMPLETE = [
        'payment_method' => 'string',
        'boleto.type' => 'string',
        'boleto.instructions' => 'string',
        'boleto.due_at' => 'date_format:Y-m-d\TH:i:s\Z',
        'boleto.document_number' => 'string',
        'boleto.interest' => 'array',
        'boleto.interest.days' => 'integer',
        'boleto.interest.type' => 'string',
        'boleto.interest.amount' => 'numeric',
        'boleto.fine' => 'array',
        'boleto.fine.days' => 'integer',
        'boleto.fine.type' => 'string',
        'boleto.fine.amount' => 'numeric',
    ];

    // Para testes com Pix, na sua Dashboard vá em Configurações > meios de pagamento > Pix > modelo de negócio > Selecione "Simulator"
    public const ORDER_PAYMENT_PIX = [
        'payment_method' => 'string',
        'pix.expires_in' => 'integer', // Tempo em segundos
        'pix.additional_information' => 'array',
        'pix.additional_information.*.name' => 'string',
        'pix.additional_information.*.value' => 'string',
    ];

    public const ORDER_PAYMENT_CHECKOUT = [
        'payment_method' => 'string',
        'checkout' => 'array',
        'checkout.expires_in' => 'integer',
        'checkout.default_payment_method' => 'string',
        'checkout.customer_editable' => 'boolean',
        'checkout.billing_address' => 'array',
        'checkout.billing_address_editable' => 'boolean',
        'checkout.skip_checkout_success_page' => 'boolean',
        'checkout.accepted_payment_methods' => 'array',
        'checkout.accepted_payment_methods.*' => 'string',
        'checkout.accepted_brands' => 'array',
        'checkout.accepted_brands.*' => 'string',
        'checkout.accepted_multi_payment_methods' => 'array',
        'checkout.accepted_multi_payment_methods.*' => 'array',
        'checkout.success_url' => 'string',
        'checkout.credit_card.operation_type' => 'string',
        'checkout.credit_card.statement_descriptor' => 'string|max:13',
        'checkout.credit_card.installments' => 'array',
        'checkout.credit_card.installments.*.number' => 'integer',
        'checkout.credit_card.installments.*.total' => 'integer',
        'checkout.boleto.instructions' => 'string',
        'checkout.boleto.due_at' => 'date_format:Y-m-d\TH:i:s\Z',
        'checkout.pix.expires_in' => 'integer', // Tempo em segundos
        'checkout.pix.additional_information' => 'array',
        'checkout.pix.additional_information.*.name' => 'string',
        'checkout.pix.additional_information.*.value' => 'string',
    ];

    public const ORDER_MULTIPLE_PAYMENT_METHODS = [
        'payment_method' => 'string',
        'checkout.expires_in' => 'numeric',
        'checkout.accepted_payment_methods' => 'array',
        'checkout.accepted_payment_methods.*' => 'string',
        'checkout.accepted_multi_payment_methods' => 'array',
        'checkout.accepted_multi_payment_methods.*' => 'array',
        'checkout.accepted_multi_payment_methods.*.*' => 'string|in:CREDIT_CARD,BOLETO',
        'checkout.accepted_brands' => 'array',
        'checkout.accepted_brands.*' => 'string',
        'checkout.success_url' => 'string',
        'checkout.credit_card' => 'array',
        'checkout.boleto' => 'array',
    ];

    public const ORDER_MULTIPLE_BUYERS = [
        'amount' => 'numeric',
    ];

    public function order(array $items, array $payments, array|string $customer): array
    {
        if (is_array($customer)) {
            return [
                'closed' => true,
                'customer' => $customer,
                'items' => $items,
                'payments' => $payments,
            ];
        }

        if (is_string($customer)) {
            return [
                'closed' => true,
                'customer_id' => $customer,
                'items' => $items,
                'payments' => $payments,
            ];
        }

        throw new Exception('Customer must be an array or string');
    }
}
