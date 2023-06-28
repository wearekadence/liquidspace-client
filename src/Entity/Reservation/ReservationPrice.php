<?php

namespace LiquidSpace\Entity\Reservation;

class ReservationPrice
{
    public readonly float $amount;
    public readonly CurrencyType $currency;
    public readonly string $currencyPrefix;
    public readonly float $currencyRate;

    public function __construct(array $priceData)
    {
        $currency = CurrencyType::tryFrom($priceData['currency']);
        if (null === $currency) {
            throw new \InvalidArgumentException('Invalid currency: '.$priceData['currency']);
        }
        $this->currency = $currency;

        $this->amount = $priceData['amount'];
        $this->currencyPrefix = $priceData['currencyPrefix'];
        $this->currencyRate = $priceData['currencyRate'];
    }
}
