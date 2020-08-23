<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllProduct extends Model
{
    protected $fillable = ['id', 'name','idCategory', 'idExpansion', 'idMetaproduct', 'added'];

    public $timestamps = false;

    public function expansion(){
        return $this->hasOne('\App\Models\Expansion');
    }

    public function category(){
        return $this->hasOne('App\Models\Category');
    }

}
