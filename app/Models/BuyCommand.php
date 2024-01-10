<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyCommand extends Model
{
    protected $fillable = ['id_status','comments', 'initial_value', 'value', 'document_no'];


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

    public function ItemsWithCardAndProduct(){
        return $this->hasMany('App\Models\BuyItem', 'id_buy_command')->with('card','product', 'product.expansion', 'product.priceGuide', 'stock', 'product.image');
    }

    public function ItemsWithCardAndProductAndPriceGuide(){
        return $this->ItemsWithCardAndProduct()->with('product.priceGuide');
    }

    public function value(){
        //if($this->)
        $value = 0;
        foreach ($this->items as $item){
            $value += $item->price * $item->quantity;
        }
        return $value;
    }

    public function getStatus()
    {
        if ($this->status != null && $this->status->status != null)
            return $this->status->status->name;
        else
            return null;
    }
}
