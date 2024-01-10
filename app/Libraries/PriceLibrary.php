<?php


namespace App\Libraries;


use phpDocumentor\Reflection\Types\Self_;

class PriceLibrary
{
    public const Eur = 'Eur';
    public const Usd = 'Usd';
    public const Czk = 'Czk';

    public const RebuyCoeficient = 0.65;

    private static $rates = array(
        array('currency1' => 'Eur', 'currency2' => 'Usd', 'rate' => 1.2),
        array('currency1' => 'Eur', 'currency2' => 'Czk', 'rate' => 25),
        array('currency1' => 'Usd', 'currency2' => 'Eur', 'rate' => 0.88),
        array('currency1' => 'Usd', 'currency2' => 'Czk', 'rate' => 22),
        array('currency1' => 'Czk', 'currency2' => 'Eur', 'rate' => 1 / 25),
        array('currency1' => 'Czk', 'currency2' => 'Usd', 'rate' => 1 / 22),
    );

    private static $coeficient = [
        'MT' => 1,
        'NM' => 1,
        'EX' => 0.9,
        'GD' => 0.75,
        'LP' => 0.65,
        'PL' => 0.55,
        'PO' => 0.4,
    ];
    private static $pricesEur = array (
        0.16,0.2,0.24,0.28,0.32,0.38,0.48,0.58,0.68,0.78,0.88,0.98,1.08,1.18,1.28,1.38,1.48,1.58,1.78,1.98,2.28,2.48,2.78,2.98,3.28,3.48,3.78,3.98,4.48,4.98,5.48,5.98,6.48,6.98,7.48,7.98,8.48,8.98,9.48,9.98
    );

    public static function getCoeficient($condition) {
        return self::$coeficient[$condition] ?? 1;
    }

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

    public static function getAmount($price, $currencyFrom, $currencyTo)
    {
        if ($currencyFrom != $currencyTo) {
            $rate = self::getRate($currencyFrom, $currencyTo);

            if (count($rate) == 1)
                $price = $price * $rate[0]['rate'];

            return round($price, 2);
        }
        return $price;
    }

    public static function getPrice($price, $currencyFrom = PriceLibrary::Eur, $currencyTo = PriceLibrary::Eur)
    {
        if ($currencyFrom != $currencyTo) {
            $rate = self::getRate($currencyFrom, $currencyTo);

            if (count($rate) == 1)
                $price = $price * $rate[0]['rate'];

            if ($currencyTo == self::Czk)
                $price = self::roundCzk($price);
            if ($currencyTo == self::Eur)
                $price = self::roundEur($price);

            return round($price, 2);
        }

        if ($currencyTo == self::Eur)
            $price = self::roundEur($price);
        return $price;
    }

    private static function roundEur($price) {
        $price *= 1.1;
        foreach (self::$pricesEur as $p) {
            if($price <= $p)
                return $p;
        }
        return ceil($price) - 0.02;
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

    public static function getProductPrice($product, $isFoil = 0, $condition = "NM"): float {
        //$priceTrend = $product->lastPriceGuide->first()?->{$stock->isFoil ? 'foilTrend' : 'trend'};
        //$priceSuggested = $product->lastPriceGuide->first()?->suggested * ($stock->isFoil ? 1.3 : 1);
        //$price = $priceTrend ;//> $priceSuggested ? $priceTrend : $priceSuggested;
        $priceguide = $product->lastPriceGuide->first();

        if (!$priceguide) {
            throw new \Exception("No priceguide");
        }

        if ($isFoil) {
            $trend = "foilTrend";
            $low = 'foilLow';
            $prices = [$priceguide->$trend, $priceguide->foilAvgOne, $priceguide->foilAvgSeven, $priceguide->foilAvgThirty];
        } else {
            $trend = "trend";
            $low = 'lov';
            $prices = [$priceguide->$trend, $priceguide->avgOne, $priceguide->avgSeven, $priceguide->avgThirty];
        }
        sort($prices);
        $price = ($prices[1] + $prices[2] + $prices[3]) / 3;

        if ($price < $priceguide->$trend) {
            $price = $priceguide->$trend;
        }

        if ($price < $priceguide->$low) {
            $price = $priceguide->$low * 1.2;
        }
        if ($price < 0.01) {
            throw new \Exception("Price too small");
        }
        if (in_array($product->idExpansion, [5523,5522,5520,5492,5491,5490,5489,5424,5358])) {
            $price *= .95;
        }
        return round(
            self::getPrice($price) * self::$coeficient[$condition]
            , 2);
    }

    public static function getProductRebuyPrice($product, $isFoil = 0, $condition = "NM"): float
    {
        $price = round(self::getProductPrice($product,$isFoil,$condition) * self::RebuyCoeficient, 2);
        if ($price > 1) {
            $price = floor($price * 10) / 10;
        } else if ($price >= 0.9) {
            $price = 0.9;
        } else if ($price >= 0.8) {
            $price = 0.8;
        } else if ($price >= 0.7) {
            $price = 0.7;
        } else if ($price >= 0.6) {
            $price = 0.6;
        } else if ($price >= 0.5) {
            $price = 0.5;
        } else if ($price >= 0.4) {
            $price = 0.4;
        } else if ($price >= 0.3) {
            $price = 0.3;
        } else if ($price >= 0.25) {
            $price = 0.24;
        } else if ($price >= 0.2) {
            $price = 0.2;
        } else if ($price >= 0.16) {
            $price = 0.16;
        } else if ($price >= 0.1) {
            $price = 0.08;
        } else {
            $price = 0.02;
        }
        return $price;
    }
}
