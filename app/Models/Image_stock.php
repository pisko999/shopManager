<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image_stock extends Model
{
    protected $fillable = ['path', 'alt'];
    public $timestamps = false;

    public function stock()
    {
        return $this->belongsTo('App\Models\Stock');
    }
}
