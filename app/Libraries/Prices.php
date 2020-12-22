<?php


namespace App\Libraries;


class Prices
{

    public static function getPriceCZK($price)
    {
        $p = $price * 26.5; //eur/czk
        if ($p % 10 == 0)
            $p++;

        if ($p > 75 || ($p > 20 && $p % 10 > 5))
            $p = (ceil($p / 10) * 10) - 1;
        elseif ($p > 20 && $p % 10 <= 5)
            $p = (ceil($p / 10) * 10) - 5;
        elseif ($p > 15)
            $p = 19;
        elseif ($p > 12)
            $p = 15;
        elseif ($p > 9)
            $p = 12;
        elseif ($p > 7)
            $p = 9;
        elseif ($p > 5)
            $p = 5;
        elseif ($p < 4)
            $p = 4;

        return $p;
    }
}
