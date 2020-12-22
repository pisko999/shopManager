<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{

    protected $fillable = ['evaluation_grade', 'item_description', 'packaging', 'speed', 'comment'];

    public $timestamps = false;

    public function Complaints(){
        return $this->belongsToMany('App\Models\Complaint');
    }
}
