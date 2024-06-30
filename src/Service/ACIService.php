<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * ACIService handles interactions with the ACI (Alternative Payments) API
 */
class ACIService
{
    /**
     * @var string
     */
    private string $apiKey;
    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    /**
     * @param HttpClientInterface $httpClient
     * @param string $apiKey
     */
    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    /**
     * @param array $paymentDetails
     * @return array
     */
    public function processPayment(array $paymentDetails): array
    {
        $url = 'https://test.oppwa.com/v1/payments';

        // Hardcoded parameters only for test task
        $authKey = 'OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg';
        $entityId = '8a8294174b7ecb28014b9699220015ca';
        $paymentBrand = 'VISA';
        $cardNumber = $paymentDetails['cardNumber'];
        $currency = 'EUR';

        $response = $this->httpClient->request('POST', $url, [
            'auth_basic' => $authKey,
            'json' => [
                'entityId' => $entityId,
                'amount' => $paymentDetails['amount'],
                'currency' => $currency,
                'paymentType' => 'DB',
                'paymentBrand' => $paymentBrand,
                'card.number' => $cardNumber,
                'card.expiryYear' => $paymentDetails['cardExpYear'],
                'card.expiryMonth' => $paymentDetails['cardExpMonth'],
                'card.cvv' => $paymentDetails['cardCvv'],
            ],
        ]);

        $responseData = $response->toArray();

        return [
            'transactionId' => $responseData['id'] ?? null,
            'date' => date('Y-m-d H:i:s'),
            'amount' => $paymentDetails['amount'],
            'currency' => $currency,
            'cardBin' => substr($cardNumber, 0, 6),
        ];
    }
}
