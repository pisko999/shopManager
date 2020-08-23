<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorderColor extends Model
{
    protected $fillable = ['id', 'color'];

    public $timestamps = false;

    public function Cards(){
        return $this->belongsToMany('\App\Models\Cards');
    }
}
