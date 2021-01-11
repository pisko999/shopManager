<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 21/03/2019
 * Time: 16:59
 */

namespace App\Repositories;

use App\Http\Requests\ItemAddRequest;
use \App\Models\Item;
use \App\Models\Command;
use App\Models\ShippingMethod;
use App\Models\Status;
use App\Models\StatusName;
use App\Services\StockService;
use http\Env\Request;

class ShippingMethodRepository extends ModelRepository implements ShippingMethodRepositoryInterface
{
    private $methodRepository;

    public function __construct(MethodRepositoryInterface $methodRepository,ShippingMethod $shippingMethod)
    {
        $this->methodRepository = $methodRepository;
        $this->model = $shippingMethod;
    }

    public function createFromMKM($data){
        $method = $this->methodRepository->getOrCreateByName($data);

        return $this->model->firstOrCreate(
            [
                'method_id' => $method->id,
                'price' => $data->price,
                'is_letter' => $data->isLetter,
                'is_insured' => $data->isInsured,
            ]
        );
    }

}
