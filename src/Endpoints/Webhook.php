<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Endpoints;

use Anisotton\Pagarme\Utils\ApiAdapter;

class Webhook extends ApiAdapter
{
    /**
     * Criar webhook
     */
    public function create(array $data)
    {
        return $this->post('hooks', $data);
    }

    /**
     * Obter webhook
     */
    public function find(string $id)
    {
        return $this->get("hooks/{$id}");
    }

    /**
     * Listar webhooks
     */
    public function all(array $queryParams = [])
    {
        return $this->get('hooks', $queryParams);
    }

    /**
     * Atualizar webhook
     */
    public function update(string $id, array $data)
    {
        return $this->put("hooks/{$id}", $data);
    }

    /**
     * Deletar webhook
     */
    public function deleteWebhook(string $id)
    {
        return $this->delete("hooks/{$id}");
    }

    /**
     * Verificar assinatura do webhook
     */
    public function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Processar payload do webhook
     */
    public function processWebhook(string $payload, string $signature = null): array
    {
        if ($signature && config('pagarme.webhook_secret')) {
            if (!$this->verifySignature($payload, $signature, config('pagarme.webhook_secret'))) {
                throw new \Exception('Invalid webhook signature');
            }
        }

        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON payload');
        }

        return $data;
    }
}
