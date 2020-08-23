<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cmc extends Model
{
    protected $fillable = ['id', 'cmc'];

    public $timestamps = false;

    public function Cards(){
        return $this->belongsToMany('\App\Models\Cards');
    }
}
