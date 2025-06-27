<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Utils;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use Throwable;

abstract class ApiAdapter
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    protected function getHeader(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.base64_encode(config('pagarme.api_key').':'),
            'Accept' => 'application/json',
            'User-Agent' => 'Pagarme-Laravel/'.$this->getPackageVersion(),
        ];
    }

    protected function getFormDataHeader(): array
    {
        return [
            'Content-Type' => 'multipart/form-data',
            'Authorization' => 'Basic '.base64_encode(config('pagarme.api_key').':'),
            'Accept' => 'application/json',
            'User-Agent' => 'Pagarme-Laravel/'.$this->getPackageVersion(),
        ];
    }

    protected function getPackageVersion(): string
    {
        return '1.0.0';
    }

    protected function getUrl(string $url): string
    {
        $baseUrl = config('pagarme.base_url');

        if (! str_ends_with($baseUrl, '/')) {
            $baseUrl .= '/';
        }

        $apiVersion = config('pagarme.api_version');

        if (! str_ends_with($apiVersion, '/')) {
            $apiVersion .= '/';
        }

        return $baseUrl.$apiVersion.ltrim($url, '/');
    }

    public function post(string $url, array $data = [], bool $multipart = false): ResponseInterface
    {
        $fullUrl = $this->getUrl($url);
        $options = $this->setHeaders($multipart, $data);

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

    public function put(string $url, array $data = [], bool $multipart = false): ResponseInterface
    {
        $fullUrl = $this->getUrl($url);
        $options = $this->setHeaders($multipart, $data);

        try {
            $response = $this->client->request('PUT', $fullUrl, $options);
            $this->logRequest('PUT', $fullUrl, $data);

            return $response;
        } catch (RequestException $e) {
            $this->handleException($e);
        } catch (GuzzleException $e) {
            throw new Exception('HTTP Request failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function patch(string $url, array $data = [], bool $multipart = false): ResponseInterface
    {
        $fullUrl = $this->getUrl($url);
        $options = $this->setHeaders($multipart, $data);

        try {
            $response = $this->client->request('PATCH', $fullUrl, $options);
            $this->logRequest('PATCH', $fullUrl, $data);

            return $response;
        } catch (RequestException $e) {
            $this->handleException($e);
        } catch (GuzzleException $e) {
            throw new Exception('HTTP Request failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function get(string $url, array $queryParams = [], bool $multipart = false): ResponseInterface
    {
        $fullUrl = $this->getUrl($url);
        $options = $this->setHeaders($multipart);

        if (! empty($queryParams)) {
            $options['query'] = $queryParams;
        }

        try {
            $response = $this->client->request('GET', $fullUrl, $options);
            $this->logRequest('GET', $fullUrl, $queryParams);

            return $response;
        } catch (RequestException $e) {
            $this->handleException($e);
        } catch (GuzzleException $e) {
            throw new Exception('HTTP Request failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function delete(string $url, bool $multipart = false): ResponseInterface
    {
        $fullUrl = $this->getUrl($url);
        $options = $this->setHeaders($multipart);

        try {
            $response = $this->client->request('DELETE', $fullUrl, $options);
            $this->logRequest('DELETE', $fullUrl);

            return $response;
        } catch (RequestException $e) {
            $this->handleException($e);
        } catch (GuzzleException $e) {
            throw new Exception('HTTP Request failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function handleException(RequestException $e): never
    {
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            $errorData = json_decode($body, true);

            $this->logError($statusCode, $body, $e);

            if (json_last_error() === JSON_ERROR_NONE && isset($errorData['message'])) {
                throw new Exception($errorData['message'], $statusCode, $e);
            }

            throw new Exception("HTTP {$statusCode}: {$body}", $statusCode, $e);
        }

        throw new Exception('Request failed: '.$e->getMessage(), $e->getCode(), $e);
    }

    protected function setHeaders(bool $multipart = false, ?array $data = null): array
    {
        $options = [];

        if ($multipart) {
            $options['headers'] = $this->getFormDataHeader();
            if ($data) {
                $options['multipart'] = $this->buildMultipartData($data);
            }
        } else {
            $options['headers'] = $this->getHeader();
            if ($data) {
                $options['json'] = $data;
            }
        }

        return $options;
    }

    protected function buildMultipartData(array $data): array
    {
        $multipart = [];

        foreach ($data as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => is_array($value) ? json_encode($value) : (string) $value,
            ];
        }

        return $multipart;
    }

    protected function logRequest(string $method, string $url, array $data = []): void
    {
        if (config('pagarme.log_requests', false)) {
            Log::info('Pagarme API Request', [
                'method' => $method,
                'url' => $url,
                'data' => $this->sanitizeLogData($data),
            ]);
        }
    }

    protected function logError(int $statusCode, string $body, Throwable $exception): void
    {
        Log::error('Pagarme API Error', [
            'status_code' => $statusCode,
            'response_body' => $body,
            'exception' => $exception->getMessage(),
        ]);
    }

    protected function sanitizeLogData(array $data): array
    {
        $sensitiveFields = ['card', 'cvv', 'password', 'token'];

        return $this->recursivelyHideSensitiveData($data, $sensitiveFields);
    }

    protected function recursivelyHideSensitiveData(array $data, array $sensitiveFields): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveFields)) {
                $data[$key] = '***HIDDEN***';
            } elseif (is_array($value)) {
                $data[$key] = $this->recursivelyHideSensitiveData($value, $sensitiveFields);
            }
        }

        return $data;
    }
}
