<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{

    protected $fillable = ['name'];

    public $timestamps = false;

    public function Evaluations(){
        return $this->belongsToMany('App\Models\Evaluation');
    }
}
