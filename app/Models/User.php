<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mkm_id', 'mkm_username', 'mkm_country', 'mkm_is_commercial',
        'mkm_reputation', 'mkm_risk_group', 'mkm_ships_fast', 'mkm_sell_count',
        'name', 'forename', 'country_code', 'phone', 'email', 'address_id', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function addresses()
    {
        return $this->hasMany('\App\Models\Address');
    }

    public function Commands(){
        return $this->hasMany('App\Models\Commnads');
    }

    public function BuyCommands(){
        return $this->hasMany('App\Models\BuyCommand', 'id_client','id');
    }
    public function getActualBuyCommand(){
        return $this->BuyCommands()->where('id_status', 2)->first();
    }
}
