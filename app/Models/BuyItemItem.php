<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyItemItem extends Model
{
    protected $fillable = ['id_buy_item','id_item', 'quantity'];
    public $timestamps = false;

    public function BuyItem(){
        return $this->belongsTo('App\Models\buyItem', 'id_buy_item','id');
    }

    public function Item(){
        return $this->belongsTo('App\Models\Item', 'id_item','id');
    }

}
