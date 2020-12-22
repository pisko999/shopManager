<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{

    protected $fillable = ['method_id', 'price', 'is_letter', 'is_insured'];

    public $timestamps = false;

    public function method()
    {
        return $this->belongsTo('App\Models\Method');
    }
}
