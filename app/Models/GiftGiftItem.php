<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GiftGiftItem extends Pivot
{
    protected $table = "gift_gift_item";
    protected $fillable = ['quantity'];

    public $timestamps = false;

    public function Gift()
    {
        return $this->belongsTo(Gift::class);
    }

    public function GiftItem()
    {
        return $this->belongsTo(GiftItem::class);
    }

    public function GiftList()
    {
        return $this->hasOneThrough(GiftList::class, GiftItem::class);
    }
}
