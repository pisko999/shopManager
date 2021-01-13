<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardDeck extends Model
{
    protected $fillable = ['quantity','foil', 'price', 'sideboard'];
    public $timestamps = false;

    public function MetaProduct(){
        return $this->belongsTo(AllProduct::class,'metaproduct_id','idMetaproduct');
    }

    public function Product(){
        return $this->belongsTo(AllProduct::class);
    }

    public function Stock(){
        return $this->belongsTo(Stock::class);
    }

}
