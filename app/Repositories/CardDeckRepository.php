<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 21/03/2019
 * Time: 16:59
 */

namespace App\Repositories;

use App\Http\Requests\ItemAddRequest;
use App\Models\Address;
use App\Models\CardDeck;
use App\Models\Complaint;
use \App\Models\Item;
use \App\Models\Command;
use App\Models\ShippingMethod;
use App\Models\Status;
use App\Models\StatusName;
use App\Services\StockService;
use http\Env\Request;

class CardDeckRepository extends ModelRepository implements CardDeckRepositoryInterface
{

    public function __construct(CardDeck $cardDeck)
    {
        $this->model = $cardDeck;
    }

    public function increase($id,$quantity){

        $this->model = $this->getById($id);
        $this->model->quantity += $quantity;
        $this->model->save();
        return $this->model;
    }
    public function decrease($id,$quantity){

        $this->model = $this->getById($id);
        $this->model->quantity -= $quantity;
        if($this->model->quantity < 0)
            return $this->remove($id);
        $this->model->save();
        return $this->model;
    }

    public function remove($id){
        return $this->getById($id)->remove();
    }

    public function createFromMKM($data){
        return $this->model->firstOrNew([
            'name' => $data->name,
            'extra' => $data->extra,
            'street' => $data->street,
            'postal' => $data->zip,
            'city' => $data->city,
            'country' => $data->country,
        ]);
    }

}
