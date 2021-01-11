<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['status_id', 'date_bought', 'date_paid', 'date_sent', 'date_received', 'date_canceled', 'reason', 'was_merged_into'];
    public $timestamps = false;

    public function Name(){
        return $this->belongsTo('App\Models\StatusName', 'status_id');
    }

    public function Status(){
        return $this->belongsTo('App\Models\StatusName','status_id','id');
        //return \App\Objects\Status::getStatus($this->status_id);
    }

    public function StatusName(){
        return $this->status->name;
    }
}
