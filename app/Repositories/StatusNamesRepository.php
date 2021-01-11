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
use App\Models\StatusName;
use App\Services\StockService;
use http\Env\Request;

class StatusNamesRepository extends ModelRepository implements StatusNamesRepositoryInterface
{

    public function __construct(StatusName $statusName)
    {
        $this->model = $statusName;
    }

    public function getOrCreateByName($name)
    {
        //var_dump($name);

        return $this->model->firstOrCreate(['name' => $name]);
    }
    public function getByType($type){
        return $this->model->where('name', '=', $type)->first();
    }

}
