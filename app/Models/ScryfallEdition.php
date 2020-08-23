<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScryfallEdition extends Model
{

    protected $fillable = [
        'id',
        'name',
        'code',
        'uriScryfall',
        'uriSearch',
        'setType',
        'cardCount',
        'parentSetCode',
        'iconSVGUri'
    ];

    public $timestamps = false;

    public function Expansions(){
        return $this->belongsToMany('App\Models\Expansion',
            'expansion_scryfall_edition',
            'scryfall_edition_code',
            'expansion_id',
            'code',
            'id'
        );
}
}
