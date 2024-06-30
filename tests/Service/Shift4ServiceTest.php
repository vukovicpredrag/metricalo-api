<?php

namespace App\Tests\Service;

use App\Service\Shift4Service;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Shift4Service tests
 */
class Shift4ServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testProcessPayment()
    {
        // Mock HttpClientInterface
        $httpClient = $this->createMock(HttpClientInterface::class);

        // Configure the mock to return a mocked response
        $httpClient->method('request')->willReturn(new MockResponse(json_encode(['transactionId' => 'shift4-123', 'status' => 'success']), [
            'http_code' => 200,
        ]));

        // Create Shift4Service instance with mocked HttpClientInterface
        $shift4Service = new Shift4Service($httpClient, 'pr_test_tXHm9qV9qV9bjIRHcQr9PLPa');

        // Test payment details
        $paymentDetails = [
            'amount' => 100,
            'currency' => 'USD',
            'cardNumber' => '1234567890123456',
            'cardExpYear' => '2025',
            'cardExpMonth' => '01',
            'cardCvv' => '123',
        ];

        // Call the method under test
        $result = $shift4Service->processPayment($paymentDetails);

        // Assertions
        $this->assertArrayHasKey('transactionId', $result);
        $this->assertArrayHasKey('date', $result);
        $this->assertEquals($paymentDetails['amount'], $result['amount']);
        $this->assertEquals($paymentDetails['currency'], $result['currency']);
        $this->assertEquals(substr($paymentDetails['cardNumber'], 0, 6), $result['cardBin']);
    }
}
