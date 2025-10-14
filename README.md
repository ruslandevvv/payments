# Payment System

Система обработки платежей с поддержкой нескольких способов оплаты, банков и гибким расчетом комиссий.

## Возможности

### Множественные способы оплаты
- **Card (Карты)** - оплата кредитными/дебетовыми картами
- **Qiwi** - оплата через QIWI кошелек (номер телефона)

### Интеграция с банками
- **Sberbank** - поддержка Card и Qiwi, RUB и EUR
- **Tinkoff** - только карты, только RUB, минимум 15000 RUB, комиссия 2.5%

### Перекладывание комиссии на покупателя
Система позволяет переложить комиссию на покупателя. При этом рассчитывается такая сумма платежа, 
чтобы продавец получил ровно требуемую сумму после вычета комиссии.

**Пример:**
- Продавец хочет получить: 100 RUB
- Комиссия: 4% + 1 RUB (min 3 RUB)
- Покупатель заплатит 105.16 RUB
- Комиссия 5.16 RUB
- Продавец получит 100 RUB

### Система уведомлений
Автоматическая отправка уведомлений после успешного платежа для:
- Всех платежей через Qiwi
- Платежей картой в валюте EUR

### Обработка ошибок
Система корректно обрабатывает:
- **HTTP 500** - ошибка от банка
- **Timeout** - таймаут соединения с банком
- **ProcessingException** - общие ошибки обработки платежа


### Пример использования в коде
```php
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

// Платеж картой
$card = new Card(
    '4242424242424242', 
    new DateTime('2025-10-09'), 
    123, 
    new Currency('RUB')
);

$payment = $createPaymentService->handle(
    new CreatePaymentCommand(
        Money::RUB(10000), 
        $card, 
        true
    )
);

// Проведение платежа
try {
    $response = $chargePaymentService->handle($payment);

    if ($response->isCompleted()) {
        echo 'Payment completed successfully!';
    }
} catch (PaymentException $e) {
    echo 'Payment failed: ' . $e->getMessage();
}

// Платеж через Qiwi
$qiwi = new Qiwi('+79001234567', new Currency('RUB'));

$payment = $createPaymentService->handle(
    new CreatePaymentCommand(
        Money::RUB(20000), 
        $qiwi, 
        true
    )
);

// Перекладывание комиссии на покупателя
$payment = $createPaymentService->handle(
    new CreatePaymentCommand(
        Money::RUB(10000), 
        $card, 
        false
    )
);
```

### Добавление нового способа оплаты
```php
class Sbp implements PaymentMethod
{
    private string $phoneNumber;
    private Currency $currency;
    
    public function __construct(string $phoneNumber, Currency $currency)
    {
        $this->phoneNumber = $phoneNumber;
        $this->currency = $currency;
    }
    
    public function getType(): string
    {
        return 'sbp';
    }
    
    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
```

### Добавление нового банка
```php
class Alfa implements Bank
{
    public function getName(): string
    {
        return 'alfa';
    }
    
    public function createPayment(Money $amount, PaymentMethod $paymentMethod): Payment
    {
        // Логика интеграции с API банка
    }
    
    public function supports(PaymentMethod $paymentMethod, Money $amount): bool
    {
        // Логика проверки поддержки
    }
}
```

### Добавление новых правил комиссий
Добавлять в `InMemoryCommissionRuleProvider`:
```php
// 1 RUB = 100, 1000 RUB = 100000
$rules[] = new CommissionRule(
    'alfa',
    'sbp',
    'RUB',
    Money::RUB(100),  
    Money::RUB(100000),
    1.5,
    Money::RUB(0),
    Money::RUB(200)
);
```

