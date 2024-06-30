<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * ACIService handles interactions with the ACI (Alternative Payments) API
 */
class ACIService
{
    private string $apiKey;
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    /**
     * Process a payment with ACI API.
     *
     * @param array $paymentDetails Array containing 'amount', 'cardNumber', 'cardExpYear', 'cardExpMonth', 'cardCvv'
     * @return array Response data including 'transactionId', 'date', 'amount', 'currency', 'cardBin'
     */
    public function processPayment(array $paymentDetails): array
    {
        $url = 'https://test.oppwa.com/v1/payments';

        // Example hardcoded parameters for testing, should be replaced with actual dynamic values or environment variables
        $authKey = 'OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg';
        $entityId = '8a8294174b7ecb28014b9699220015ca';
        $paymentBrand = 'VISA';
        $cardNumber = $paymentDetails['cardNumber'];
        $currency = 'EUR';

        try {
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
        } catch (\Exception $e) {
            return [
                'error' => 'Payment processing failed: ' . $e->getMessage(),
            ];
        }
    }
}
