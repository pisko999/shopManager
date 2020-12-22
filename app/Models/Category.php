<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['id', 'name', 'haveArticles'];

    public $timestamps = false;

    public function all_products(){
        return $this->belongsToMany('App\Models\AllProducts');
    }

}
