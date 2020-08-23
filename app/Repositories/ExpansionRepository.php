<?php


namespace App\Repositories;


use App\Models\Expansion;
use App\Models\ExpansionsLocalisation;
use App\Models\Language;

class ExpansionRepository extends ModelRepository implements ExpansionRepositoryInterface
{
    public function __construct(Expansion $expansion)
    {
        $this->model = $expansion;
    }

    public function add($expansion)
    {
        $newExpansion = Expansion::firstOrCreate(
            [
                'name' => $expansion->enName
            ],
            [
                'idMKM' => $expansion->idExpansion,
                //'symbol_path' => '',
                'sign' => $expansion->abbreviation,
                //'type' => ,
                'release_date' => substr($expansion->releaseDate,0, 10),
                'isReleased' => $expansion->isReleased
            ]);

        foreach ($expansion->localization as $localization) {
            //\Debugbar::info($localization->languageName);
            $language = Language::firstOrCreate(
                [
                    'id' => $localization->idLanguage
                ],
                [
                    'languageName' => $localization->languageName
                ]);

            $localization = ExpansionsLocalisation::firstOrCreate(
                [
                    'idExpansion' => $newExpansion->id,
                    'idLanguage' => $language->id,
                ],
                [
                    'name' => $localization->name
                ]);
        }


        return $expansion;
    }

    public function getAllWithScryfallEditions(){
        return $this->model->with('ScryfallEditions')->get();
    }

    public function getByIdWithScryfallEditions($id){
        return $this->model->whereId($id)->with('ScryfallEditions')->first();
    }

    public function getNonLinked(){
        return $this->model->with('ScryfallEditions')->get()->filter(function ($e){
            return $e->ScryfallEditions->count() == 0;
        });
    }

    public function getLinked(){
        return $this->model->with('ScryfallEditions')->get()->filter(function ($e){
            return $e->ScryfallEditions->count() != 0;
        });
    }


}
