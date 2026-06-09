<?php

namespace Wesender;

class WesenderException extends \RuntimeException
{
    public function __construct(string $message, public readonly int $statusCode = 0)
    {
        parent::__construct($message);
    }
}

class Wesender
{
    private const BASE_URL = 'https://api.wesender.nl';

    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl = self::BASE_URL,
    ) {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('API key is verplicht');
        }
    }

    // E-mails
    public function sendEmail(array $params): array
    {
        return $this->post('/emails', $params);
    }

    public function sendBatch(array $emails): array
    {
        return $this->post('/emails/batch', ['emails' => $emails])['data'];
    }

    public function getEmail(string $id): array
    {
        return $this->get("/emails/{$id}");
    }

    public function listEmails(int $limit = 50): array
    {
        return $this->get("/emails?limit={$limit}")['data'];
    }

    // Domeinen
    public function listDomains(): array
    {
        return $this->get('/domains')['data'];
    }

    public function createDomain(string $domain): array
    {
        return $this->post('/domains', ['domain' => $domain]);
    }

    public function deleteDomain(string $id): array
    {
        return $this->delete("/domains/{$id}");
    }

    // Webhooks
    public function listWebhooks(): array
    {
        return $this->get('/webhooks')['data'];
    }

    public function createWebhook(string $url, array $events): array
    {
        return $this->post('/webhooks', ['url' => $url, 'events' => $events]);
    }

    // HTTP helpers
    private function get(string $path): array
    {
        return $this->request('GET', $path);
    }

    private function post(string $path, array $body): array
    {
        return $this->request('POST', $path, $body);
    }

    private function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    private function request(string $method, string $path, ?array $body = null): array
    {
        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$this->apiKey}",
                "Content-Type: application/json",
                "User-Agent: wesender-php/1.0.0",
            ],
            CURLOPT_POSTFIELDS     => $body ? json_encode($body) : null,
        ]);

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true) ?? [];

        if ($status >= 400) {
            throw new WesenderException($data['error'] ?? "HTTP {$status}", $status);
        }

        return $data;
    }
}
