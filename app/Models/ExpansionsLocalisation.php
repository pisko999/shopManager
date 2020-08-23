<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpansionsLocalisation extends Model
{

    protected $fillable = ['idExpansion', 'name', 'idLanguage'];

    public $timestamps = false;


    public function language()
    {
        return $this->hasOne('App\Models\Language');
    }


    public function expansion()
    {
        return $this->belongsTo('App\Models\Expansion');
    }
}
