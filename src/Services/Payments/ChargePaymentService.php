<?php

namespace App\Services\Payments;

use App\Banks\Responses\Payment as PaymentResponse;
use App\Entities\Payment;
use App\Services\Payments\Errors\HttpErrorException;
use App\Services\Payments\Errors\PaymentException;
use App\Services\Payments\Errors\ProcessingException;
use App\Services\Payments\Errors\TimeoutException;
use App\Services\Payments\Notifications\Notifier;

class ChargePaymentService
{
    private BankResolver $bankResolver;
    private Notifier $notifier;

    public function __construct(BankResolver $bankResolver, Notifier $notifier)
    {
        $this->bankResolver = $bankResolver;
        $this->notifier = $notifier;
    }

    /**
     * Проводит платеж через банк
     *
     * @param Payment $payment
     * @return PaymentResponse
     * @throws PaymentException
     */
    public function handle(Payment $payment): PaymentResponse
    {
        try {
            // Получаем банк для проведения платежа
            $bank = $this->bankResolver->resolve(
                $payment->getPaymentMethod(),
                $payment->getAmount()
            );

            // Проводим платеж через банк
            $response = $bank->createPayment(
                $payment->getAmount(),
                $payment->getPaymentMethod()
            );

            // Если платеж успешен, отправляем уведомление (если нужно)
            if ($response->isCompleted()) {
                $this->notifier->notifyIfNeeded($payment);
            }

            return $response;

        } catch (HttpErrorException $e) {
            // Обрабатываем ошибки HTTP от банка
            throw new ProcessingException(
                sprintf('Bank API error (HTTP %d): %s', $e->getHttpCode(), $e->getMessage()),
                $e
            );
        } catch (TimeoutException $e) {
            // Обрабатываем таймауты соединения с банком
            throw new ProcessingException(
                sprintf('Bank connection timeout: %s', $e->getMessage()),
                $e
            );
        } catch (PaymentException $e) {
            // Пробрасываем PaymentException дальше
            throw $e;
        } catch (\Exception $e) {
            // Обрабатываем все остальные непредвиденные ошибки
            throw new ProcessingException(
                sprintf('Unexpected error during payment processing: %s', $e->getMessage()),
                $e
            );
        }
    }
}
