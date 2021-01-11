<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 21/03/2019
 * Time: 16:59
 */

namespace App\Repositories;

use App\Http\Requests\ItemAddRequest;
use App\Models\Complaint;
use \App\Models\Item;
use \App\Models\Command;
use App\Models\ShippingMethod;
use App\Models\Status;
use App\Models\StatusName;
use App\Services\StockService;
use http\Env\Request;

class ComplaintRepository extends ModelRepository implements ComplaintRepositoryInterface
{

    public function __construct(Complaint $complaint)
    {
        $this->model = $complaint;
    }

    public function createFromMKM($data){
        return $this->model->firstOrCreate(['name' => $data]);
    }

}
