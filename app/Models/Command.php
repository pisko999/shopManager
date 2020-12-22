<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $fillable = ['id', 'client_id', 'billing_address_id', 'delivery_address_id', 'payment_id', 'status_id', 'idOrderMKM', ' tracking_number', 'temporary_email', 'is_presale'];

    public function client()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function storekeeper()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function payment()
    {
        return $this->belongsTo('App\Models\Payment');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Status');
    }

    public function billing_address()
    {
        return $this->belongsTo('App\Models\Address');
    }

    public function delivery_address()
    {
        return $this->belongsTo('App\Models\Address');
    }

    public function shippingMethod(){
        return $this->belongsTo('App\Models\ShippingMethod');
    }

    public function items()
    {
        return $this->hasMany('App\Models\Item');
    }

    public function amount()
    {
        $amount = 0;
        foreach ($this->items as $item) {
            $amount += $item->quantity * $item->price;
        }
        return $amount;
    }
}
