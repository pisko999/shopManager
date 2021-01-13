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
use App\Models\Complaint;
use App\Models\Deck;
use \App\Models\Item;
use \App\Models\Command;
use App\Models\ShippingMethod;
use App\Models\Status;
use App\Models\StatusName;
use App\Services\StockService;
use http\Env\Request;

class DeckRepository extends ModelRepository implements DeckRepositoryInterface
{

    public function __construct(Deck $deck)
    {
        $this->model = $deck;
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
