<?php


namespace App\Repositories;


use App\models\AllProduct;
use App\Models\BuyCommand;
use App\Models\BuyItem;
use App\models\Categories;
use App\Models\Expansion;
use App\Models\ExpansionsLocalisation;
use App\Models\Language;

class BuyCommandRepository extends ModelRepository implements BuyCommandRepositoryInterface
{
    public function __construct(BuyCommand $buyCommand)
    {
        $this->model = $buyCommand;
    }

    public function add($data)
    {
        return;
    }

    public function new(){
        $buyCommand = \Auth::user()->BuyCommands()->create();
        return $buyCommand;
    }

    public function saveEdition($data){
        $buyCommand = \Auth::user()->getActualBuyCommand();
        if(!$buyCommand)
            $buyCommand = $this->new();

        foreach ($data as $key => $value){
            if(str_contains($key,'quantity') && $value != null && $value > 0)
            $buyCommand->Items()->save(new BuyItem(['id_product' => substr($key,8),'price' => 0,'quantity' => $value]));
        }
        return true;
    }

}
