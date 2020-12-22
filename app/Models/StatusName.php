<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusName extends Model
{
    protected $fillable = ['id', 'name'];
    public $timestamps = false;

    public function Statuses(){
        return $this->belongsToMany('App\Models\Status');
    }
}
