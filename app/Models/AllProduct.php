<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllProduct extends Model
{
    protected $fillable = ['id', 'name', 'MKMCollectorNumber','idCategory', 'idExpansion', 'idMetaproduct', 'added'];

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

    public function getStock($foil=0){
        return $this->stock()->where('isFoil', $foil)->get();
    }

    public function priceGuide(){
        $date = \Carbon\Carbon::now()->toDateString();
        return $this->hasMany('App\Models\PriceGuide', 'idProduct', 'id')->where('date', $date)->orderBy('date','desc');
    }
}
