<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['id', 'shortName', 'name'];

    public $timestamps = false;

    public function ExpansionLanguage()
    {
        return $this->belongsToMany('App\Models\ExpansionsLocalisation');
    }
}
