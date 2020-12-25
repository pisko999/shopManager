<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;

class Expansion extends Model
{

    protected $fillable = ['idMKM', 'name', 'symbol_path', 'sign', 'type', 'release_date', 'isReleased','added'];

    public $timestamps = false;

    public function ScryfallEditions(){
        return $this->belongsToMany('App\Models\ScryfallEdition',
            'expansion_scryfall_edition',
            'expansion_id',
            'scryfall_edition_code',
            'id',
            'code'
        );
    }

    public function AllProducts(){
        return $this->hasMany('\App\Models\AllProduct','idExpansion', 'idMKM');
    }

    public function languages()
    {
        return $this->hasMany('App\Models\ExpansionsLocalisation');
    }

    public function AllCards(){
        return $this->AllProducts()->where('idCategory',1);
    }

    public function AllCardsWithRelationsPaginate(){
        return $this->AllProducts()
            ->where('idCategory',1)
            ->orderByRaw('LENGTH(MKMCollectorNumber)', 'ASC')
            ->orderBy('MKMCollectorNumber')
            ->with('card','stock','image','card.stock','card.rarity')
            ->paginate(50)
            ->appends(request()->only('id', 'foils'));
    }

    public function CardsCount(){
        return $this->AllCards()->count();
    }

    public function CardsToVerify(){
        return $this->hasMany('\App\Models\CardVerification')->where('verified', false);
    }

    public function CardsToBeAdded(){
        return $this->AllCards()->where('added',false);
    }

    public function link($edition){
        $this->ScryfallEditions()->attach($edition);
        //$this->added = false;
        $this->type = $edition->setType;
        $this->save();
    }

}
