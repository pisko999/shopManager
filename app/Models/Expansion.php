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

    public function AllCardsWithBasicRelations(){
        $lands = ['Plains','Island','Swamp','Mountain','Forest'];
        return $this->AllProducts()
            ->where('idCategory',1)
            ->whereNotIn('name',$lands)
            ->whereHas('card',function($q){
                $q->whereDoesntHave('types',function ($q){
                    $q->where('name', '=','Token');
                });
            })
            ->join('cards', 'cards.id', '=', 'all_products.id')
            ->with('card','card.colors','card.rarity', 'card.cardFaces', 'card.types')
            ->orderByRaw('LENGTH(cards.scryfallCollectorNumber)', 'ASC')
            ->orderBy('cards.scryfallCollectorNumber');
    }

    public function AllCardsWithRelationsPaginate(){
        return $this->AllProducts()
            ->where('idCategory',1)
            ->whereHas('card',function($q){
                $q->whereDoesntHave('types',function ($q){
                    $q->where('name', '=','Token');
                });
            })
            ->join('cards', 'cards.id', '=', 'all_products.id')
            ->with('card','stock','image','card.stock','card.rarity')
            ->orderByRaw('LENGTH(cards.scryfallCollectorNumber)', 'ASC')
            ->orderBy('cards.scryfallCollectorNumber')
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
