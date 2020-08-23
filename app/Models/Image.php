<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['alt', 'path','product_id'];

    public $timestamps = false;

    public function Product(){
        return $this->belongsTo('\App\Models\AllProduct');
    }
}
