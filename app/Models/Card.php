<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{

    protected $fillable = [
        'id',
        'foil',
        'nonfoil',
        'oversized',
        'reserved',
        'booster',
        'scryfallCollectorNumber',
        'fullArt',
        'promo',
        'story_spotlight',
        'textless'
    ];

    public $timestamps = false;
    public $incrementing = false;

    public function product()
    {
        return $this->belongsTo('\App\Models\AllProduct', 'id','id');
    }

    public function CardFaces(){
        return $this->belongsToMany('\App\Models\CardFace');
    }

    public function Types()
    {
        return $this->belongsToMany('\App\Models\CardType');
    }

    public function SpellTypes()
    {
        return $this->belongsToMany('\App\Models\SpellType');
    }

    public function ActivatedAbilities(){
        return $this->belongsToMany('\App\Models\ActivatedAbility', 'card_activated_ability');
    }

    public function TriggeredAbilities(){
        return $this->belongsToMany('\App\Models\TriggeredAbility');
    }

    public function StaticAbilities(){
        return $this->belongsToMany('\App\Models\StaticAbility');
    }

    public function colors(){
        return $this->belongsToMany('\App\Models\Color');
    }

    public function ColorIdentities(){
        return $this->belongsToMany('\App\Models\Color','card_color_identity');
    }

    public function Creature(){
        return $this->belongsTo('\App\Models\Creature');
    }

    public function Planeswalker(){
        return $this->belongsTo('\App\Models\Planeswalker');
    }

    public function ManaCost(){
        return $this->belongsTo('\App\Models\ManaCost');
    }

    public function Cmc(){
        return $this->belongsTo('\App\Models\Cmc');
    }

    public function BorderColor(){
        return $this->belongsTo('\App\Models\BorderColor');
    }

    public function PromoType(){
        return $this->belongsTo('\App\Models\PromoType');
    }

    public function Rarity(){
        return $this->belongsTo('\App\Models\Rarity');
    }

    public function expansion(){
        return $this->Product->expansion();
    }

    public function stock()
    {
        return $this->belongsTo('\App\Models\Stock', 'id', 'all_product_id');
    }
}
