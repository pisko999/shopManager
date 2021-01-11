<?php


namespace App\Objects;


class Conditions
{
    private static $conditions =[
        "MT" => "Mint",
        "NM" => "Near Mint",
        "EX" => "Excellent",
        "GD" => "Good",
        "LP" => "Lightly played",
        "PL" => "Played",
        "PO" => "Poor",
        ];

    public static function getConditions(){
        return self::$conditions;
    }
}
