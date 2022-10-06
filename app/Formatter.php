<?php

namespace Otomaties\Events;

class Formatter
{
    /**
     * Format amount with currency symbol
     *
     * @param integer|float|string $amount
     * @return string
     */
    public static function currency(int|float|string $amount) : string
    {
        return '€ ' . $amount;
    }
}
