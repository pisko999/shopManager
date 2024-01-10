<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftItem extends Model
{
    protected $fillable=['gift_list_id', 'all_product_id', 'quantity', 'quantity_rest', 'foil'];
    public $timestamps = false;

    public function GiftList(){
        return $this->belongsTo(GiftList::class);
    }

    public function Gifts(){
        return $this->belongsToMany(Gift::class)->using(GiftGiftItem::class)->withPivot('quantity as quantity_used');
    }

    public function giftGiftItems()
    {
        return $this->hasMany(GiftGiftItem::class);
    }

    public function Product(){
        return $this->belongsTo(AllProduct::class,'all_product_id', 'id');
    }
}
