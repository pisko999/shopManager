<?php

namespace App\Models;

use App\Services\MKMService;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['id', 'all_product_id', 'initial_price', 'quantity', 'price', 'language', 'state', 'idArticleMKM', 'stock', 'isFoil','signed','playset','altered', 'on_sale', 'comments'];

    public function product()
    {
        return $this->belongsTo('App\Models\AllProduct','all_product_id', 'id');
    }

    public function card()
    {
        return $this->belongsTo('App\Models\Card','all_product_id', 'id');
    }

    public function image()
    {
        return $this->hasOne('App\Models\Image_stock');
    }

    public function items()
    {
        return $this->hasMany('App\Models\Item');
    }

    public function BuyItems(){
        return $this->hasMany('App\Models\BuyItem','id_stock','idS');
    }
/*
    public function addToMKM($p)
    {
        $mkm = new MKMService();
        $mkmProduct = $p;//$mkm->getProduct($this->product->idProductMKM);
        $this->checkPrice($mkmProduct);
        $quantity = $this->quantity > 30 ? 30 : $this->quantity;
        //\Debugbar::info($quantity);
        if ($quantity > 0) {
            $answer = $mkm->addToStock($this->product->idProductMKM, $quantity, $this->getPriceEur(), $this->state, $this->language, '', $this->product->card->foil == 1 ? "true" : "false");
            try {
                $this->idArticleMKM = $answer->inserted[0]->idArticle->idArticle;

            } catch (\Exception $e) {
                //\Debugbar::info($answer);
            }
            \Debugbar::info($answer);
            $this->save();
        }
    }

    public function checkOnMKM($p)
    {
        if ($this->product->idProductMKM == null)
            return;

        $mkm = new MKMService();

        $mkmProduct = $p;//$mkm->getProduct($this->product->idProductMKM);
        //\Debugbar::info($mkmProduct);
        $this->checkPrice($mkmProduct);
        //\Debugbar::info($this->idArticleMKM);

        if ($this->idArticleMKM == null) {
            $this->addToMKM();
            return;
        }

        $mkmArticle = $mkm->getArticle($this->idArticleMKM);
        //\Debugbar::info($mkmArticle);
        $priceDiff = $mkmArticle->article->price - $this->getPriceEur();
        if (
            $priceDiff / $mkmArticle->article->price > 1.05 ||
            $priceDiff / $mkmArticle->article->price < .95 ||
            $mkmArticle->article->count != $this->quantity)
            $mkm->changeArticleInStock($this->idArticleMKM, $this->quantity, $this->getPriceEur(), $this->state, $this->language,'', $this->product->card->foil == 1? 'true':'false');

    }

    private function checkPrice($mkmProduct)
    {
        if ($this->product->idProductMKM == null)
            return -1;

        $this->price = $this->getPriceKc($this->product->card->foil ? $mkmProduct->product->priceGuide->TRENDFOIL : $mkmProduct->product->priceGuide->TREND);
        $this->save();
    }

    private function getPriceKc($price)
    {
        $p = $price * 25.5;
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

    private function getPriceEur()
    {
        return $this->price / 25.5;
    }
*/
}
