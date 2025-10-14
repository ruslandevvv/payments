<?php

use App\Banks\Sberbank;
use App\Banks\Tinkoff;
use App\Domain\Payments\CommissionPolicy;
use App\Infrastructure\Payments\InMemoryCommissionRuleProvider;
use App\PaymentMethods\Card;
use App\PaymentMethods\Qiwi;
use App\Services\Payments\BankResolver;
use App\Services\Payments\BuyerGrossAmountCalculator;
use App\Services\Payments\ChargePaymentService;
use App\Services\Payments\Commands\CreatePaymentCommand;
use App\Services\Payments\Commissions\CommissionCalculator;
use App\Services\Payments\CreatePaymentService;
use App\Services\Payments\Notifications\Notifier;
use Money\Currency;
use Money\Money;

require_once './vendor/autoload.php';


// Инициализация зависимостей
$commissionRuleProvider = new InMemoryCommissionRuleProvider();
$commissionCalculator = new CommissionCalculator();
$grossAmountCalculator = new BuyerGrossAmountCalculator($commissionCalculator);
$banks = [
    new Sberbank(), 
    new Tinkoff()
];
$bankResolver = new BankResolver($banks, $commissionRuleProvider);
$notifier = new Notifier(new CommissionPolicy());

$createPaymentService = new CreatePaymentService(
    $commissionRuleProvider,
    $commissionCalculator,
    $grossAmountCalculator,
    $bankResolver
);
$chargePaymentService = new ChargePaymentService($bankResolver, $notifier);

// Оплата картой в RUB через Sberbank (сумма 100 руб, комиссия с продавца)
try {
    $card = new Card(
        '4242424242424242', 
        new \DateTime('2025-12-31'), 
        123, 
        new Currency('RUB')
    );

    $payment = $createPaymentService->handle(
        new CreatePaymentCommand(
            Money::RUB(10000), 
            $card, true
        )
    );
    
    echo sprintf(
        "
        Payment created:\n  
        Amount: %s RUB\n  
        Commission: %s RUB\n  
        Seller receives: %s RUB\n  
        Bank: %s\n
        ",
        $payment->getAmount()->getAmount() / 100,
        $payment->getCommission()->getAmount() / 100,
        $payment->getNetAmount()->getAmount() / 100,
        $payment->getBankName()
    );
    
    $response = $chargePaymentService->handle($payment);

    if ($response->isCompleted()) {
        echo "Payment completed successfully!\n";
    }
} catch (\Exception $e) {
    echo "Payment failed: " . $e->getMessage() . "\n";
}


// Оплата картой в RUB с перекладыванием комиссии на покупателя
try {
    $card = new Card(
        '4242424242424242',
         new \DateTime('2025-12-31'), 
         123, 
         new Currency('RUB')
    );
    $payment = $createPaymentService->handle(
        new CreatePaymentCommand(
            Money::RUB(10000), 
            $card, 
            false
        )
    );
    
    echo sprintf(
        "
        Payment created:\n  
        Buyer pays: %s RUB\n  
        Commission: %s RUB\n  
        Seller receives: %s RUB (exactly 100 RUB)\n  
        Bank: %s\n
        ",
        $payment->getAmount()->getAmount() / 100,
        $payment->getCommission()->getAmount() / 100,
        $payment->getNetAmount()->getAmount() / 100,
        $payment->getBankName()
    );
    
    $response = $chargePaymentService->handle($payment);

    if ($response->isCompleted()) {
        echo "Payment completed successfully!\n";
    }
} catch (\Exception $e) {
    echo "Payment failed: " . $e->getMessage() . "\n";
}

// Оплата картой в EUR (с уведомлением)
try {
    $card = new Card(
        '4242424242424242', 
        new \DateTime('2025-12-31'), 
        123, 
        new Currency('EUR')
    );

    $payment = $createPaymentService->handle(
        new CreatePaymentCommand(
            Money::EUR(5000), 
            $card,
             true
        )
    );
    
    echo sprintf(
        "
        Payment created:\n  
        Amount: %s EUR\n  
        Commission: %s EUR\n  
        Seller receives: %s EUR\n 
        Bank: %s\n
        ",
        $payment->getAmount()->getAmount() / 100,
        $payment->getCommission()->getAmount() / 100,
        $payment->getNetAmount()->getAmount() / 100,
        $payment->getBankName()
    );
    
    $response = $chargePaymentService->handle($payment);

    if ($response->isCompleted()) {
        echo "Payment completed successfully!\n";
        echo "Notification sent (EUR card payment)\n";
    }
} catch (\Exception $e) {
    echo "Payment failed: " . $e->getMessage() . "\n";
}


// Оплата через Qiwi (с уведомлением)
try {
    $qiwi = new Qiwi(
        '+79001234567', 
        new Currency('RUB')
    );

    $payment = $createPaymentService->handle(
        new CreatePaymentCommand(
            Money::RUB(20000), 
            $qiwi, 
            true
        )
    );
    
    echo sprintf(
        "
        Payment created:\n  
        Amount: %s RUB\n  
        Commission: %s RUB\n  
        Seller receives: %s RUB\n  
        Bank: %s\n  
        Phone: %s\n
        ",
        $payment->getAmount()->getAmount() / 100,
        $payment->getCommission()->getAmount() / 100,
        $payment->getNetAmount()->getAmount() / 100,
        $payment->getBankName(),
        $qiwi->getPhoneNumber()
    );
    
    $response = $chargePaymentService->handle($payment);

    if ($response->isCompleted()) {
        echo "Payment completed successfully!\n";
        echo "Notification sent (Qiwi payment)\n";
    }
} catch (\Exception $e) {
    echo "Payment failed: " . $e->getMessage() . "\n";
}


// Оплата через Tinkoff (только карты от 15000 RUB)
try {
    $card = new Card(
        '5536913800000000', 
        new \DateTime('2025-12-31'), 
        123, 
        new Currency('RUB')
    );

    $payment = $createPaymentService->handle(
        new CreatePaymentCommand(
            Money::RUB(1500000), 
            $card, 
            true
        )
    );
    
    echo sprintf(
        "
        Payment created:\n  
        Amount: %s RUB\n  
        Commission: %s RUB (2.5%%)\n  
        Seller receives: %s RUB\n  
        Bank: %s\n
        ",
        $payment->getAmount()->getAmount() / 100,
        $payment->getCommission()->getAmount() / 100,
        $payment->getNetAmount()->getAmount() / 100,
        $payment->getBankName()
    );
    
    $response = $chargePaymentService->handle($payment);

    if ($response->isCompleted()) {
        echo "Payment completed successfully!\n";
    }
} catch (\Exception $e) {
    echo "Payment failed: " . $e->getMessage() . "\n";
}


// Попытка платежа с суммой меньше минимальной для Tinkoff (должна отправиться через Sberbank)
try {
    $card = new Card(
        '4242424242424242', 
        new \DateTime('2025-12-31'), 
        123, 
        new Currency('RUB')
    );

    $payment = $createPaymentService->handle(
        new CreatePaymentCommand(
            Money::RUB(5000), 
            $card, true
        )
    );
    
    echo sprintf(
        "
        Payment created:\n  
        Amount: %s RUB\n  
        Commission: %s RUB\n  
        Seller receives: %s RUB\n  
        Bank: %s (automatically selected)\n
        ",
        $payment->getAmount()->getAmount() / 100,
        $payment->getCommission()->getAmount() / 100,
        $payment->getNetAmount()->getAmount() / 100,
        $payment->getBankName()
    );
    
    $response = $chargePaymentService->handle($payment);

    if ($response->isCompleted()) {
        echo "Payment completed successfully!\n";
    }
} catch (\Exception $e) {
    echo "Payment failed: " . $e->getMessage() . "\n";
}