<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CardVerification extends Model
{
protected $fillable = ['scryfall_card_id'];
    public $timestamps = false;

    public function Card(){
        return $this->belongsTo('\App\Models\AllProduct', 'all_product_id');
    }
    public function Expansion(){
        return $this->belongsTo('\App\Models\Expansion');
    }
    public function ScryfallEdition(){
        return $this->belongsTo('\App\Models\ScryfallEdition');
    }
}
