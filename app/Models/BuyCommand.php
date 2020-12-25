<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyCommand extends Model
{
    protected $fillable = ['id_status','comments'];


    public function Client(){
        return $this->belongsTo('App\Models\User', 'id_client','id');
    }

    public function Storekeeper(){
        return $this->belongsTo('App\Models\User', 'id_storekeeper','id');
    }

    public function Payment(){
        return $this->belongsTo('App\Models\Payment', 'id_payment','id');
    }

    public function Stock(){
        return $this->belongsTo('App\Models\Status', 'id_status','id');
    }

    public function Status(){
        return $this->belongsTo('App\Models\Status', 'id_status','id');
    }

    public function Items(){
        return $this->hasMany('App\Models\BuyItem', 'id_buy_command');
    }

    public function value(){
        $value = 0;
        foreach ($this->items as $item){
            $value += $item->price * $item->quantity;
        }
        return $value;
    }
}
