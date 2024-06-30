<?php

namespace App\Tests\Service;

use App\Service\ACIService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * ACIService tests
 */
class ACIServiceTest extends TestCase
{
    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testProcessPayment()
    {
        // Mock HttpClientInterface
        $httpClient = $this->createMock(HttpClientInterface::class);

        // Configure the mock to return a mocked response
        $httpClient->method('request')->willReturn(new MockResponse(json_encode(['id' => 'aci-123', 'result' => 'success']), [
            'http_code' => 200,
        ]));

        // Create ACIService instance with mocked HttpClientInterface
        $aciService = new ACIService($httpClient, 'OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg');

        // Test payment details
        $paymentDetails = [
            'amount' => 100,
            'currency' => 'EUR',
            'cardNumber' => '1234567890123456',
            'cardExpYear' => '2025',
            'cardExpMonth' => '01',
            'cardCvv' => '123',
        ];

        $result = $aciService->processPayment($paymentDetails);

        // Assertions
        $this->assertEquals($paymentDetails['amount'], 100);
        $this->assertEquals($paymentDetails['currency'], 'EUR');
        $this->assertEquals(substr($paymentDetails['cardNumber'], 0, 6), $result['cardBin']);
    }
}
