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


    public function getStandartEditions()
    {
        $standart_types = array("core", "expansion");

        return $this->model
            ->select('idMKM', 'name')
            ->where('release_date', '>', date("Y") - 2 . "-09-01")
            ->whereIn('type', $standart_types)
            ->orderBy('release_date', 'desc')
            ->get();
    }

    public function getArrayForSelect()
    {
        $modern_types = array("core", "expansion");

        $mh1 = $this->model
            ->select('idMKM', 'name')
            ->where('sign', 'mh1')
            ->first();

        $standart = $this->getStandartEditions();

        $modern = $this->model
            ->select('idMKM', 'name')
            ->where('release_date', '>', "2003-07-01")
            ->where('release_date', '<', date("Y") - 2 . "-09-01")
            ->whereIn('type', $modern_types)
            ->orderBy('release_date', 'desc')
            ->get();
        $legacy = $this->model
            ->select('idMKM', 'name')
            ->where('release_date', '<', "2003-07-01")
            ->whereIn('type', $modern_types)
            ->orderBy('release_date', 'desc')
            ->get();

        $master = $this->model
            ->select('idMKM', 'name')
            ->where('type', 'masters')
            ->orderBy('release_date', 'desc')
            ->get();

        $funny = $this->model
            ->select('idMKM', 'name')
            ->where('type', 'funny')
            ->orderBy('release_date', 'desc')
            ->get();

        $masterpieces = $this->model
            ->select('idMKM', 'name')
            ->where('type', 'masterpiece')
            ->orderBy('release_date', 'desc')
            ->get();

        $draft_inovations = $this->model
            ->select('idMKM', 'name')
            ->where('type', 'draft_innovation')
            ->where('sign', '<>', 'mh1')
            ->orderBy('release_date', 'desc')
            ->get();

        $promos = $this->model
            ->select('idMKM', 'name')
            ->where('type', 'promo')
            ->orderBy('release_date', 'desc')
            ->get();

        $typesArray = array_merge($modern_types, ['masters', 'funny', 'masterpiece', 'draft_inovations', 'promo']);

        $others = $this->model
            ->select('idMKM', 'name')
            ->whereNotIn('type', $typesArray)
            ->orderBy('release_date', 'desc')
            ->get();

        foreach ($standart as $edition)
            $standartArray[$edition->idMKM] = $edition->name;

        if ($mh1 != null)
            $modernArray[$mh1->idMKM] = $mh1->name;

        foreach ($modern as $edition)
            $modernArray[$edition->idMKM] = $edition->name;

        foreach ($legacy as $edition)
            $legacyArray[$edition->idMKM] = $edition->name;

        foreach ($master as $edition)
            $masterArray[$edition->idMKM] = $edition->name;

        foreach ($funny as $edition)
            $funnyArray[$edition->idMKM] = $edition->name;

        foreach ($masterpieces as $edition)
            $masterpiecesArray[$edition->idMKM] = $edition->name;

        foreach ($draft_inovations as $edition)
            $draft_inovationsArray[$edition->idMKM] = $edition->name;

        foreach ($promos as $edition)
            $promosArray[$edition->idMKM] = $edition->name;

        foreach ($others as $edition)
            $othersArray[$edition->idMKM] = $edition->name;


        $selectArray = array(0 => "All");
        if (isset($standartArray))
            $selectArray["standard"] = $standartArray;
        if (isset($modernArray))
            $selectArray["modern"] = $modernArray;
        if (isset($legacyArray))
            $selectArray["legacy"] = $legacyArray;
        if (isset($masterArray))
            $selectArray["masters"] = $masterArray;
        if (isset($funnyArray))
            $selectArray["funny"] = $funnyArray;
        if (isset($masterpiecesArray))
            $selectArray["masterpieces"] = $masterpiecesArray;
        if (isset($draft_inovationsArray))
            $selectArray["draft_inovations"] = $draft_inovationsArray;
        if (isset($promosArray))
            $selectArray["promos"] = $promosArray;
        if (isset($othersArray))
            $selectArray["others"] = $othersArray;


        return $selectArray;

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

    public function getByMKMId($id){
        return $this->model->where('idMKM',$id)->first();
    }

}
