<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Creature extends Model
{
    protected $fillable = ['id', 'power','toughness'];

    public $timestamps = false;

    public function Cards(){
        return $this->belongsToMany('\App\Models\Cards');
    }

    public function CardFacess(){
        return $this->belongsToMany('\App\Models\CardFaces');
    }
}
