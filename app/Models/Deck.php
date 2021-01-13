<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    protected $fillable=['name', 'user_id'];

    public function User(){
        return $this->belongsTo(User::class);
    }

    public function Cards(){
        return $this->hasMany(CardDeck::class,);
    }
}
