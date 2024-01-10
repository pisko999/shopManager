<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyItem extends Model
{
    protected $fillable = ['id_product','id_stock', 'id_buy_command', 'id_language', 'price','quantity','state','isFoil','playset','signed','altered', 'added', 'sold_quantity', 'is_new'];
    public $timestamps = false;

    public function Stock(){
        return $this->belongsTo('App\Models\Stock', 'id_stock','id');
    }

    public function Product(){
        return $this->belongsTo('App\Models\AllProduct', 'id_product','id');
    }
    public function Card(){
        return $this->belongsTo('App\Models\Card', 'id_product','id');
    }

    public function BuyCommand(){
        return $this->belongsTo('App\Models\BuyCommand', 'id_buy_commnand','id');
    }

    public function Language(){
        return $this->belongsTo('App\Models\Language', 'id_language','id');
    }

    public function items()
    {
        return $this->belongsToMany('App\Models\Item','buy_item_items', 'id_buy_item', 'id_item')->withPivot('quantity');
    }
}
