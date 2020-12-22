<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllProduct extends Model
{
    protected $fillable = ['id', 'name', 'idCategory', 'idExpansion', 'idMetaproduct', 'added'];

    public $timestamps = false;

    public function expansion()
    {
        return $this->hasOne('\App\Models\Expansion','idMKM','idExpansion');
    }

    public function category()
    {
        return $this->hasOne('App\Models\Category');
    }

    public function stock()
    {
        return $this->hasMany('App\Models\Stock');
    }

    public function image()
    {
        return $this->hasOne('App\Models\Image','product_id');
    }

    public function card(){
        return $this->hasOne('App\Models\Card','id','id');

    }
}
