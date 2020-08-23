<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planeswalker extends Model
{
    protected $fillable = ['id', 'loyalty'];

    public $timestamps = false;

    public function Cards(){
        return $this->belongsToMany('\App\Models\Cards');
    }

    public function CardFacess(){
        return $this->belongsToMany('\App\Models\CardFaces');
    }
}
