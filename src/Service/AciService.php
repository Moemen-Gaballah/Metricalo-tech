<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AciService implements PaymentServiceInterface
{
    private HttpClientInterface $client;
    private string $authKey;
    private string $entityId;

    public function __construct(HttpClientInterface $client, string $authKey, string $entityId)
    {
        $this->client = $client;
        $this->authKey = $authKey;
        $this->entityId = $entityId;
    }

    public function purchase(array $data): array
    {
        $data['entityId'] = $this->entityId;
        $response = $this->client->request('POST', 'https://api.aci.com/v1/payment', [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->authKey,
            ],
        ]);

        $body = $response->toArray();

        return [
            'transaction_id' => $body['transactionRef'],
            'created_at' => $body['timestamp'],
            'amount' => $body['amount'],
            'currency' => $body['currency'],
            'card_bin' => substr($body['card']['number'], 0, 6),
        ];
    }
}
