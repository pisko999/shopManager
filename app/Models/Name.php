<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    protected $fillable = ['idProduct', 'name', 'idLanguage'];

    public $timestamps = false;

    public function Product(){
        return $this->belongsTo('\App\Models\AllProducts', 'idProduct', 'id');
    }
}
