<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Shift4Service implements PaymentServiceInterface
{
    private HttpClientInterface $client;
    private string $authKey;

    public function __construct(HttpClientInterface $client, string $authKey)
    {
        $this->client = $client;
        $this->authKey = $authKey;
    }

    public function purchase(array $data): array
    {
        $response = $this->client->request('POST', 'https://api.shift4.com/v1/charge', [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->authKey,
            ],
        ]);

        $body = $response->toArray();

        return [
            'transaction_id' => $body['id'],
            'created_at' => $body['created'],
            'amount' => $body['amount'],
            'currency' => $body['currency'],
            'card_bin' => substr($body['source']['card']['number'], 0, 6),
        ];
    }
}
