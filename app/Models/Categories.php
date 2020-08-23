<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $fillable = ['id', 'name'];

    public $timestamps = false;

    public function all_products(){
        return $this->belongsToMany('App\Models\AllProducts');
    }

}
