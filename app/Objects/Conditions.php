<?php


namespace App\Objects;


class Conditions
{
    public const MT = "MT";
    public const NM = "NM";
    public const LP = "LP";
    public const PL = "PL";
    public const Ex = "Ex";
    public const GD = "GD";
    public const PO = "PO";

    private static $conditions = array(
        'MT'=>'Mint',
        'NM'=>'Near Mint',
        'LP'=>'Lightly Played',
        'PL'=>'Played',
        'Ex'=>'Excellent',
        'GD'=>'Good',
        'PO'=>'Poor',
    );

    public static function getCondition($id){
        return self::$conditions[$id];
    }

    public static function getKey($condition){
        return array_search($condition, self::$conditions);
    }

    public static function getConditions(){
        return self::$conditions;
    }
}
