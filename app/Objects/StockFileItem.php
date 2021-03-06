<?php


namespace App\Objects;


class StockFileItem
{
    public $idArticle;
    public $idProduct;
    public $enName;
    public $locName;
    public $expCode;
    public $expName;
    public $price;
    public $language;
    public $condition;
    public $foil;
    public $signed;
    public $playset;
    public $altered;
    public $comments;
    public $amount;
    public $onSale;
    public $idCurrency;
    public $currencyCode;

    public function __construct($data)
    {
        $this->idArticle = $data[0];
        $this->idProduct = $data[1];
        $this->enName = $data[2];
        $this->locName = $data[3];
        $this->expCode = $data[4];
        $this->expName = $data[5];
        $this->price = $data[6];
        $this->language = $data[7];
        $this->condition = $data[8];
        $this->foil = $data[9] == "X" ? 1 : 0;
        $this->signed = $data[10] == "X" ? 1 : 0;
        $this->playset = $data[11] == "X" ? 1 : 0;
        $this->altered = $data[12] == "X" ? 1 : 0;
        if ($data[13] != "Ask for scans")
            $this->comments = $data[13];
        $this->amount = $data[14];
        $this->onSale = $data[15];
        $this->idCurrency = $data[16];
        $this->currencyCode = $data[17];
    }

}
