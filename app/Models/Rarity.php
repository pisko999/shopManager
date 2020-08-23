<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rarity extends Model
{
    protected $fillable = ['id', 'name'];

    public $timestamps = false;

    public function Cards(){
        return $this->hasMany('\App\Models\Cards');
    }
}
