<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoType extends Model
{
    protected $fillable = ['id', 'type'];

    public $timestamps = false;

    public function Cards(){
        return $this->belongsToMany('\App\Models\Cards');
    }
}
