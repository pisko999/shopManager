<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftList extends Model
{
    protected $fillable=['name', 'status'];

    public function GiftItems(){
        return $this->hasMany(GiftItem::class);
    }
    public function FreeGiftItems(){
        return $this->GiftItems()->where('quantity_rest', '>', 0);
    }

    public function Gifts()
    {
        $giftList = $this->load('GiftItems.GiftGiftItems.Gift');
        $gifts = collect();
        $giftList->GiftItems->each(function ($giftItem) use ($gifts) {
            $giftItem->giftGiftItems->each(function ($giftGiftItem) use ($gifts) {
                $gifts->push($giftGiftItem->Gift);
            });
        });
        return $gifts->unique('id');
    }
    public function giftGiftItems(){
        return $this->hasMany(GiftGiftItem::class);
    }

}
