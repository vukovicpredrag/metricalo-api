<?php

namespace App\Command;

use App\Service\Shift4Service;
use App\Service\ACIService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Payment Command for running payment services
 */
class PaymentCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:payment';

    /**
     * @var Shift4Service
     */
    private Shift4Service $shift4Service;
    /**
     * @var ACIService
     */
    private ACIService $aciService;

    /**
     * @param Shift4Service $shift4Service
     * @param ACIService $aciService
     */
    public function __construct(Shift4Service $shift4Service, ACIService $aciService)
    {
        $this->shift4Service = $shift4Service;
        $this->aciService = $aciService;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription('Process payment via Shift4 or ACI')
            ->addArgument('gateway', InputArgument::REQUIRED, 'Payment gateway to use: shift4 or aci')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount to charge')
            ->addArgument('currency', InputArgument::REQUIRED, 'Currency code')
            ->addArgument('cardNumber', InputArgument::REQUIRED, 'Card number')
            ->addArgument('cardExpYear', InputArgument::REQUIRED, 'Card expiration year')
            ->addArgument('cardExpMonth', InputArgument::REQUIRED, 'Card expiration month')
            ->addArgument('cardCvv', InputArgument::REQUIRED, 'Card CVV');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $gateway = $input->getArgument('gateway');
        $amount = $input->getArgument('amount');
        $currency = $input->getArgument('currency');
        $cardNumber = $input->getArgument('cardNumber');
        $cardExpYear = $input->getArgument('cardExpYear');
        $cardExpMonth = $input->getArgument('cardExpMonth');
        $cardCvv = $input->getArgument('cardCvv');

        $paymentDetails = [
            'amount' => $amount,
            'currency' => $currency,
            'cardNumber' => $cardNumber,
            'cardExpYear' => $cardExpYear,
            'cardExpMonth' => $cardExpMonth,
            'cardCvv' => $cardCvv
        ];

        if ($gateway === 'shift4') {
            $result = $this->shift4Service->processPayment($paymentDetails);
        } elseif ($gateway === 'aci') {
            $result = $this->aciService->processPayment($paymentDetails);
        } else {
            throw new \InvalidArgumentException('Invalid payment gateway specified.');
        }

        // Output result to console
        $output->writeln(json_encode($result, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
