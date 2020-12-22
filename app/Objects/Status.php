<?php


namespace App\Objects;


class Status
{

    private static $statuses = array(
        1=>'Cart',
        2=>'Rebuy',
        3=>'Want',
        4=>'Comfirmed',
        5=>'Waiting payment',
        6=>'Paid',
        7=>'Prepared',
        8=>'Send',
        9=>'Delivered',
        10=>'Deck',
        );

    public static function getStatus($id){
        return self::$statuses[$id];
    }

    public static function getId($status){
        return array_search($status, self::$statuses);
    }
}
