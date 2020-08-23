<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardFace extends Model
{
    protected $fillable = ['id', 'name','collector_number'];

    public $timestamps = false;

    public function Cards(){
        return $this->hasOne('\App\Models\Cards');
    }

    public function CardTypes()
    {
        return $this->belongsToMany('\App\Models\CardType');
    }

    public function SpellTypes()
    {
        return $this->belongsToMany('\App\Models\SpellType');
    }

    public function ActivatedAbilities(){
        return $this->belongsToMany('\App\Models\ActivatedAbility');
    }

    public function TriggeredAbilities(){
        return $this->belongsToMany('\App\Models\TriggeredAbility');
    }

    public function StaticAbilities(){
        return $this->belongsToMany('\App\Models\StaticAbility');
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
}
