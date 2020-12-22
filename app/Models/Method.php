<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Method extends Model
{

    protected $fillable = ['id', 'name'];

    public $timestamps = false;

    public function ShippingMethods(){
        return $this->hasMany('App\Models\ShippingMethods');
    }
}
