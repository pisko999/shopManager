<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['status_id', 'date_bought', 'date_paid', 'date_sent', 'date_received'];
    public $timestamps = false;

    public function Name(){
        return $this->belongsTo('App\Models\StatusName', 'status_id');
    }

    public function Status(){
        return \App\Objects\Status::getStatus($this->status_id);
    }
}
