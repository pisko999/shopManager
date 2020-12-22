<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id','name', 'extra', 'street', 'number', 'flat', 'city', 'country', 'region', 'postal'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function commands()
    {
        return $this->hasMany('App\Models\Command');
    }
}
