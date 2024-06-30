<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Shift4Service handles interactions with the Shift4 payment gateway API
 */
class Shift4Service
{
    private string $apiKey;
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
     * Process a payment with Shift4 API.
     *
     * @param array $paymentDetails Array containing 'amount', 'cardNumber', 'cardExpYear', 'cardExpMonth', 'cardCvv', 'currency'
     * @return array Response data including 'transactionId', 'date', 'amount', 'currency', 'cardBin'
     * @throws ClientExceptionInterface If there was a client-side error (HTTP 400 series status code)
     * @throws RedirectionExceptionInterface If the API request resulted in a redirection (HTTP 300 series status code)
     * @throws ServerExceptionInterface If there was a server-side error (HTTP 500 series status code)
     * @throws TransportExceptionInterface If a network or HTTP communication error occurs
     * @throws DecodingExceptionInterface If there was an error decoding the API response
     */
    public function processPayment(array $paymentDetails): array
    {
        $url = 'https://api.shift4.com/transaction/charge/create';

        // Example hardcoded parameters for testing, replace with dynamic or environment-specific values
        $authKey = 'pr_test_tXHm9qV9qV9bjIRHcQr9PLPa';
        $cardNumber = $paymentDetails['cardNumber'];

        try {
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
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface | DecodingExceptionInterface $e) {
            return [
                'error' => 'Payment processing failed: ' . $e->getMessage(),
            ];
        }
    }
}
