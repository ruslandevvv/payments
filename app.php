<?php

use App\PaymentMethods\Card;
use App\Services\Payments\ChargePaymentService;
use App\Services\Payments\Commands\CreatePaymentCommand;
use App\Services\Payments\CreatePaymentService;
use Money\Money;

require_once './vendor/autoload.php';

$createPaymentService = new CreatePaymentService();
$card = new Card('4242424242424242', new \DateTime('2021-10-15'), 123);
$payment = $createPaymentService->handle(new CreatePaymentCommand(Money::RUB(10000), $card));
$chargePaymentService = new ChargePaymentService();
$response = $chargePaymentService->handle($payment);
if ($response->isCompleted()) {
    echo 'Thank you! Payment completed';
} elseif ($response->isFailed()) {
    echo 'Something went wrong! Try another card';
}
echo PHP_EOL;