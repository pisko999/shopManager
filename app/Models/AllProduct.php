<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllProduct extends Model
{
    protected $fillable = ['id', 'name', 'MKMCollectorNumber','idCategory', 'idExpansion', 'idMetaproduct', 'added', 'update'];

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
        return $this->hasMany('App\Models\PriceGuide', 'idProduct', 'id')->orderBy('date','desc');
    }

    public function lastPriceGuide(){
        return $this->hasMany('App\Models\PriceGuide', 'idProduct', 'id')
//            ->where('date','=', Carbon::yesterday())
            ->orderBy('date','desc');
    }

    public function stockQuantity($foil = 0){
        return $this->stock()->select(\DB::raw('all_product_id, SUM(quantity) as squantity'))->where('isFoil', '=', 0)->groupBy('all_product_id')->havingRaw('squantity < 8');
    }

    public function enName() {
        return $this->hasOne('App\Models\Name', 'idProduct', 'id');
    }

    public function getName() {
        return $this->enName?->name ?? $this->name;
    }
}
