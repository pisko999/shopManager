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

    public function setSent(){
        if($this->status->name != 'paid')
            return false;
        $this->status()->associate(StatusName::firstOrCreate(['name'=> 'sent']));
        $this->date_sent =  date("Y-m-d H:i:s");
        $this->save();
        return $this;
    }

    public function setSold(){
        $this->status()->associate(StatusName::firstOrCreate(['name' => 'received']));
        if(!$this->date_paid)
            $this->date_paid = date("Y-m-d H:i:s");
        if(!$this->date_sent)
            $this->date_sent = date("Y-m-d H:i:s");
        $this->date_received = date("Y-m-d H:i:s");
        $this->save();
        return $this;
    }

    public function setPaid(){
        if($this->status->name != 'bought')
            return false;
        $this->status()->associate(StatusName::firstOrCreate(['name' => 'paid']));
        $this->date_paid = date("Y-m-d H:i:s");
        $this->save();
        return $this;
    }

    public function setCanceled($reason){
        $this->Status()->associate(StatusName::firstOrCreate(['name'=> 'canceled']));
        $this->reason = $reason;
        $this->date_canceled =  date("Y-m-d H:i:s");
        $this->status->save();
    }
}
