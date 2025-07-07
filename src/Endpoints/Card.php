<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Endpoints;

use Anisotton\Pagarme\Utils\ApiAdapter;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class Card extends ApiAdapter
{
    /**
     * Criar cartão
     */
    public function create(string $customerId, array $data): ResponseInterface
    {
        return $this->post("customers/{$customerId}/cards", $data);
    }

    /**
     * Obter cartão
     */
    public function find(string $customerId, string $cardId): ResponseInterface
    {
        return $this->get("customers/{$customerId}/cards/{$cardId}");
    }

    /**
     * Listar cartões
     */
    public function all(string $customerId, array $queryParams = []): ResponseInterface
    {
        return $this->get("customers/{$customerId}/cards", $queryParams);
    }

    /**
     * Editar cartão
     */
    public function update(string $customerId, string $cardId, array $data): ResponseInterface
    {
        return $this->put("customers/{$customerId}/cards/{$cardId}", $data);
    }

    /**
     * Excluir cartão
     */
    public function remove(string $customerId, string $cardId): ResponseInterface
    {
        return $this->delete("customers/{$customerId}/cards/{$cardId}");
    }

    /**
     * Renovar cartão
     */
    public function renew(string $customerId, string $cardId): ResponseInterface
    {
        return $this->post("customers/{$customerId}/cards/{$cardId}/renew", []);
    }

    /**
     * Criar token do cartão
     */
    public function createToken(array $data): ResponseInterface
    {
        // This endpoint uses public key authentication via appId query parameter
        $publicKey = config('pagarme.public_key');
        $queryParams = ['appId' => $publicKey];

        return $this->postWithPublicKey('tokens', $data, $queryParams);
    }

    /**
     * Obter informações do BIN
     */
    public function getBinInfo(string $bin): ResponseInterface
    {
        return $this->get("bins/{$bin}");
    }

    /**
     * POST request with public key authentication for token creation
     */
    protected function postWithPublicKey(string $url, array $data = [], array $queryParams = []): ResponseInterface
    {
        $fullUrl = $this->getUrl($url);

        // For token creation, we don't use the secret key authorization
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'Pagarme-Laravel/'.$this->getPackageVersion(),
            ],
            'json' => $data,
        ];

        if (! empty($queryParams)) {
            $options['query'] = $queryParams;
        }

        try {
            $response = $this->client->request('POST', $fullUrl, $options);
            $this->logRequest('POST', $fullUrl, $data);

            return $response;
        } catch (RequestException $e) {
            $this->handleException($e);
        } catch (GuzzleException $e) {
            throw new Exception('HTTP Request failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
