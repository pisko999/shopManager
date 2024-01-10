<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['stock_id', 'command_id', 'price', 'quantity'];

    public $timestamps = false;

    public function command()
    {
        return $this->belongsTo('App\Models\Command');
    }

    public function stock()
    {
        return $this->belongsTo('App\Models\Stock');
    }

    public function buyItems()
    {
        return $this->belongsToMany('App\Models\BuyItem','buy_item_items', 'id_item', 'id_buy_item')->withPivot('quantity');
    }
}
