<?php

namespace Otomaties\Events;

class Formatter
{
    public static function currency($amount) : string
    {
        return '€ ' . $amount;
    }
}
