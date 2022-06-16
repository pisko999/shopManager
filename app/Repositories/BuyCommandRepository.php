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
    private $statusRepository;

    public function __construct(BuyCommand $buyCommand, StatusRepositoryInterface $statusRepository)
    {
        $this->model = $buyCommand;
        $this->statusRepository = $statusRepository;
    }

    public function add($data)
    {
        return;
    }

    public function new(){
        $status = $this->statusRepository->new('rebuy');
        \Debugbar::info($status);
        $buyCommand = \Auth::user()->BuyCommands()->create(['id_status' => $status->id]);
        \Debugbar::info($buyCommand);

        return $buyCommand;
    }

    public function saveEdition($data){
        $buyCommand = \Auth::user()->getActualBuyCommand();
        if(!$buyCommand)
            $buyCommand = $this->new();
//\Debugbar::info($data);
        foreach ($data as $key => $value){
            if(str_contains($key,'quantity') && $value != null && $value > 0) {
                $buyCommand->Items()->save(new BuyItem(['id_product' => substr($key, 8), 'price' => 0, 'quantity' => $value, 'isFoil' => $data['foils']]));
            }
        }
        return true;
    }

    public function getClosed(){
        return $this->model->whereHas('status',function ($q){
            $q->whereHas('status', function ($q){
                $q->where('name','=', 'closed');
            });
        })->get();
    }

    public function getPaginateList($n = 25)
    {
        return $this->model->with('client', 'items', 'status', 'status.status')->orderBy('id', 'desc')->paginate($n);
    }

    public function setValue($buyCommand, $value) {
        $buyCommand->value = $value;
        $buyCommand->save();
    }
}
