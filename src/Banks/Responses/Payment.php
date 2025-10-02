<?php


namespace App\Banks\Responses;


class Payment
{
    public const STATUS_FAILED = 1;
    public const STATUS_COMPLETED = 2;

    private $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function isFailed(): bool
    {
        return $this->status == self::STATUS_FAILED;
    }

    public function isCompleted(): bool
    {
        return $this->status == self::STATUS_COMPLETED;
    }


}