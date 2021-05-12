<?php


namespace App\Libraries;


use phpDocumentor\Reflection\Types\Self_;

class PriceLibrary
{
    public const Eur = 'Eur';
    public const Usd = 'Usd';
    public const Czk = 'Czk';

    private static $rates = array(
        array('currency1' => 'Eur', 'currency2' => 'Usd', 'rate' => 1.2),
        array('currency1' => 'Eur', 'currency2' => 'Czk', 'rate' => 26.5),
        array('currency1' => 'Usd', 'currency2' => 'Eur', 'rate' => 0.88),
        array('currency1' => 'Usd', 'currency2' => 'Czk', 'rate' => 22),
        array('currency1' => 'Czk', 'currency2' => 'Eur', 'rate' => 0.04),
        array('currency1' => 'Czk', 'currency2' => 'Usd', 'rate' => 1 / 22),
    );

    public static function existsCurrency($currency)
    {

        return defined('self::' . $currency);
    }

    public static function getRate($currencyFrom, $currencyTo)
    {
        return array_values(
            array_filter(
                array_filter(
                    self::$rates,
                    function ($value) use ($currencyFrom) {
                        return $value['currency1'] == $currencyFrom;
                    }),
                function ($value) use ($currencyTo) {
                    return $value['currency2'] == $currencyTo;
                }
            )
        );
    }


    public static function getPrice($price, $currencyFrom, $currencyTo)
    {
        if ($currencyFrom != $currencyTo) {
            $rate = self::getRate($currencyFrom, $currencyTo);

            if (count($rate) == 1)
                $price = $price * $rate[0]['rate'];

            if ($currencyTo == self::Czk)
                $price = self::roundCzk($price);

            return round($price, 2);
        }
        return $price;
    }

    private static function roundCzk($price)
    {
        if ($price % 10 == 0)
            $price++;

        if ($price > 75 || ($price > 20 && $price % 10 > 5))
            $price = (ceil($price / 10) * 10) - 1;
        elseif ($price > 20 && $price % 10 <= 5)
            $price = (ceil($price / 10) * 10) - 5;
        elseif ($price > 15)
            $price = 19;
        elseif ($price > 12)
            $price = 15;
        elseif ($price > 9)
            $price = 12;
        elseif ($price > 7)
            $price = 9;
        elseif ($price > 5)
            $price = 5;
        elseif ($price < 5)
            $price = 4;

        return $price;
    }
}
