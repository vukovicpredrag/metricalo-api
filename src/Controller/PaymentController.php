<?php
// src/Controller/PaymentController.php

namespace App\Controller;

use App\Service\Shift4Service;
use App\Service\ACIService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private Shift4Service $shift4Service;
    private ACIService $aciService;

    public function __construct(Shift4Service $shift4Service, ACIService $aciService)
    {
        $this->shift4Service = $shift4Service;
        $this->aciService = $aciService;
    }

    /**
     * @Route("/app/example/{gateway}", methods={"POST"})
     */
    public function processPayment(Request $request, string $gateway): Response
    {
        $requestData = json_decode($request->getContent(), true);

        $paymentDetails = [
            'amount' => $requestData['amount'] ?? null,
            'currency' => $requestData['currency'] ?? null,
            'cardNumber' => $requestData['cardNumber'] ?? null,
            // Add more fields as needed
        ];

        if ($gateway === 'shift4') {
            $result = $this->shift4Service->processPayment($paymentDetails);
        } elseif ($gateway === 'aci') {
            $result = $this->aciService->processPayment($paymentDetails);
        } else {
            return $this->json(['error' => 'Invalid payment gateway specified.'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($result);
    }
}

