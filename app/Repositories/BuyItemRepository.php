<?php


namespace App\Repositories;


use App\models\AllProduct;
use App\Models\BuyCommand;
use App\Models\BuyItem;
use App\models\Categories;
use App\Models\Expansion;
use App\Models\ExpansionsLocalisation;
use App\Models\Language;

class BuyItemRepository extends ModelRepository implements BuyItemRepositoryInterface
{
    public function __construct(BuyItem $buyItem)
    {
        $this->model = $buyItem;
    }

    public function add(BuyCommand $buyCommand, $data)
    {
        if ($buyCommand->items != null)
            $item = $item = $this->getItemByDataFromBuyCommand($buyCommand, $data);


        if (!isset($item) || $item == null)
            $item = $buyCommand->Items()->create($data);

        return $item;
    }

    public function remove($id)
    {

        $item = $this->model->find($id);

        if (!$item)
            return false;

        $item->delete();

        return -1;
    }

    public function decrease($id, $data)
    {

        $item = $this->model->find($id);

        if (!$item)
            return "false3";

        $item->quantity -= $data['quantity'];
        if ($item->quantity <= 0) {
            $quantity = 0;
            $item->delete();
        } else {
            $quantity = $item->quantity;
            $item->save();
        }

        return $quantity;
    }

    public function increase($id, $data)
    {

        $item = $this->model->find($id);

        if (!$item)
            return "false2";

        $item->quantity += $data['quantity'];
        if ($item->quantity > 20)
            $item->quantity = 20;
        if ($item->quantity <= 0)
            return $this->remove($id);
        $item->save();

        return $item->quantity;
    }

    public function getByStocking(BuyCommand $buyCommand, $stocking){
        return $buyCommand->Items()->whereHas('stock', function ($q) use ($stocking){
            switch ($stocking){
                case 0:
                    return $q->where('price', '<', 0.98);
                case 1:
                    return $q->whereBetween('price',  [0.97, 1.99]);
                case 2:
                    return $q->where('price', '>', 1.98);
            }
        })->get();
    }

    //private

    private function getItemByDataFromBuyCommand(BuyCommand $buyCommand, $data)
    {
        return $buyCommand->items->filter(function ($value, $key) use ($data) {
            return $value->id_product == $data['id_product'] &&
                $value->price == $data['price'] &&
                (isset($data['isFoil']) ? $value->isFoil == $data['isFoil'] : 1) &&
                (isset($data['playset']) ? $value->playset == $data['playset'] : 1) &&
                (isset($data['signed']) ? $value->signed == $data['signed'] : 1) &&
                (isset($data['altered']) ? $value->altered == $data['altered'] : 1) &&
                (isset($data['state']) ? $value->state == $data['state'] : 1);
        })->first();
    }


}
