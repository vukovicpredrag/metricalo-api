<?php

// src/Service/Shift4Service.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Shift4Service handles interactions with the Shift4 payment gateway API
 */
class Shift4Service
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
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function processPayment(array $paymentDetails): array
    {
        $url = 'https://api.shift4.com/transaction/charge/create';

        // Hardcoded parameters only for test task
        $authKey = 'pr_test_tXHm9qV9qV9bjIRHcQr9PLPa';
        $cardNumber = $paymentDetails['cardNumber'];

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $authKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'amount' => $paymentDetails['amount'],
                'cardNumber' => $cardNumber,
                'expYear' => $paymentDetails['cardExpYear'],
                'expMonth' => $paymentDetails['cardExpMonth'],
                'cvv' => $paymentDetails['cardCvv'],
            ],
        ]);

        $responseData = $response->toArray();

        // Simplified response for demonstration
        return [
            'transactionId' => $responseData['transactionId'] ?? null,
            'date' => date('Y-m-d H:i:s'),
            'amount' => $paymentDetails['amount'],
            'currency' => $paymentDetails['currency'],
            'cardBin' => substr($cardNumber, 0, 6),
        ];
    }
}
