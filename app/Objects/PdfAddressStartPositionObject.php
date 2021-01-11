<?php


namespace App\Objects;


use App\Models\PdfAddressStartPosition;

class PdfAddressStartPositionObject
{
    private function __construct()
    {
    }

    public static function getPosition()
    {
        $startPosition = PdfAddressStartPosition::first();
        if(!$startPosition)
            $startPosition = PdfAddressStartPosition::create(['startPosition'=>0]);
        return $startPosition->startPosition;
    }

    public static function setPosition($position)
    {
        if ($position < 0 || $position > 3)
            throw new \Exception();

        $startPositon = PdfAddressStartPosition::first();
        $startPositon->startPosition = $position;
        $startPositon->save();
        return $startPositon;
    }

}
