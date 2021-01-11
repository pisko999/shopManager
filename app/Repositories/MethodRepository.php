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
use App\Models\Method;
use App\Models\Status;
use App\Models\StatusName;
use App\Services\StockService;
use http\Env\Request;

class MethodRepository extends ModelRepository implements MethodRepositoryInterface
{

    public function __construct(Method $method)
    {
        $this->model = $method;
    }

    public function getOrCreateByName($data){
        return $this->model->firstOrCreate(
            [
                'name' => $data->name
            ]
        );
    }

}
