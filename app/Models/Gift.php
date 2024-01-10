<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $fillable = ['command_id'];

    public function Command()
    {
        return $this->belongsTo(Command::class);
    }

    public function GiftItems()
    {
        return $this->belongsToMany(GiftItem::class)->using(GiftGiftItem::class)->withPivot('quantity as quantity_used');
    }
}
