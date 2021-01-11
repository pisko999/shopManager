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

    public function buyer()
    {
        return $this->client();
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

    public function shippingMethod()
    {
        return $this->belongsTo('App\Models\ShippingMethod');
    }

    public function Evaluation()
    {
        return $this->belongsTo('App\Models\Evaluation');
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

    public function setSent()
    {
        $this->status->status()->associate(StatusName::firstOrCreate(['name'=> 'sent']));
        $this->status->save();
        return $this;
    }

    public function setCanceled()
    {
        $this->status->status()->associate(StatusName::firstOrCreate(['name'=> 'canceled']));
        $this->status->save();
        return $this;
    }

    public function getStatus()
    {
        if ($this->status != null && $this->status->status != null)
            return $this->status->status->name;
        else
            return null;
    }
}
