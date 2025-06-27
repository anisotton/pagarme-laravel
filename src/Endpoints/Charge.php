<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Endpoints;

use Anisotton\Pagarme\Utils\ApiAdapter;
use Psr\Http\Message\ResponseInterface;

class Charge extends ApiAdapter
{
    /**
     * Capturar cobrança
     */
    public function capture(string $id, array $data): ResponseInterface
    {
        return $this->post("charges/{$id}/capture", $data);
    }

    /**
     * Criar cobrança
     */
    public function create(array $data): ResponseInterface
    {
        return $this->post('charges', $data);
    }

    /**
     * Obter cobrança
     */
    public function find(string $id): ResponseInterface
    {
        return $this->get("charges/{$id}");
    }

    /**
     * Editar cartão da cobrança
     */
    public function editCard(string $id, array $data): ResponseInterface
    {
        return $this->put("charges/{$id}/card", $data);
    }

    /**
     * Alterar data de vencimento
     */
    public function dueDate(string $id, array $data): ResponseInterface
    {
        return $this->put("charges/{$id}/due-date", $data);
    }

    /**
     * Alterar meio de pagamento
     */
    public function updatePaymentMethod(string $id, array $data): ResponseInterface
    {
        return $this->put("charges/{$id}/payment-method", $data);
    }

    /**
     * Cancelar cobrança
     */
    public function cancel(string $id): ResponseInterface
    {
        return $this->delete("charges/{$id}");
    }

    /**
     * Listar cobranças
     */
    public function all(array $queryParams = []): ResponseInterface
    {
        return $this->get('charges', $queryParams);
    }

    /**
     * Reprocessar cobrança
     */
    public function retry(string $id): ResponseInterface
    {
        return $this->post("charges/{$id}/retry", []);
    }

    /**
     * Confirmar pagamento em dinheiro
     */
    public function confirmCash(string $id, array $data): ResponseInterface
    {
        return $this->post("charges/{$id}/confirm-payment", $data);
    }

    /**
     * Criar cobrança PIX
     */
    public function createPix(array $data): ResponseInterface
    {
        $data['payment_method'] = 'pix';
        return $this->create($data);
    }

    /**
     * Criar cobrança Boleto
     */
    public function createBoleto(array $data): ResponseInterface
    {
        $data['payment_method'] = 'boleto';
        return $this->create($data);
    }

    /**
     * Criar cobrança com cartão de crédito
     */
    public function createCreditCard(array $data): ResponseInterface
    {
        $data['payment_method'] = 'credit_card';
        return $this->create($data);
    }
}
