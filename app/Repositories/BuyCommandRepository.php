<?php


namespace App\Repositories;


use App\Libraries\PriceLibrary;
use App\Models\AllProduct;
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
        $ids = [];
        foreach (array_keys($data) as $key) {
            $row = explode('_', $key);
            if (isset($row[2])) {
                $ids[] = $row[2];
            }
        }
        $products = AllProduct::whereIn('id', $ids)->with(['priceGuide' => function ($query) {
            $query->orderBy('date', 'desc');
        }])->get();
        foreach ($data as $key => $value){
            if(str_contains($key,'quantity') && $value != null && $value > 0) {
                $row = explode('_', $key);
                $condition = strtoupper($row[1]);
                $id = $row[2];
                $buyCommand->Items()->save(
                    new BuyItem([
                        'id_product' => $id,
                        'price' => $products->find($id)->priceGuide->first()?->{$data['foils'] ? 'foilTrend' : 'trend'} * 0.9 * PriceLibrary::getCoeficient($condition),
                        'quantity' => $value,
                        'isFoil' => $data['foils'],
                        'state' => $condition]
                    )
                );
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
    public function getBoughtByMonth($month, $year){
        return $this->model
            ->whereHas('status', function ($q) use ($month, $year) {
                $q->where('status_id', 10)->whereRaw('MONTH(date_paid) = ' . $month)->whereRaw('YEAR(date_paid) = ' . $year);
            })
            ->where('id_client', '!=', 2985)
            ->with('client', 'client.address', 'items', 'items.stock', 'items.stock.product');
    }
    public function getBoughtFromSroBeginning($month, $year){
        if ($month == 12) {
            $year++;
            $month = 1;
        } else {
            $month++;
        }
        return $this->model
            ->whereHas('status', function ($q) use ($month, $year) {
                $q->where('status_id', 10)->whereRaw('date_paid >= "2022-12-01"')->whereRaw('date_paid < "' . $year . '-' . $month . '-01"');
            })
            ->whereHas('items', function($q) {
                $q->whereRaw('sold_quantity != quantity');
            })
            ->with('client', 'client.address', 'items', 'items.stock', 'items.stock.product');
    }
}
