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
use \App\Models\Item;
use \App\Models\Command;
use App\Models\ShippingMethod;
use App\Models\Status;
use App\Models\StatusName;
use App\Models\StockChange;
use App\Services\StockService;
use http\Env\Request;

class StockChangesRepository extends ModelRepository implements StockChangesRepositoryInterface
{
    private $batch;

    public function __construct(StockChange $stockChange)
    {
        $this->model = $stockChange;
        $this->batch = intval($this->model->max('batch')) + 1;

    }

    public function add($type, $data)
    {
        return $this->model->create([
            'type' => $type,
            'stock_id' => $data[0],
            'id_article_MKM' => $data[1],
            'data1' => isset($data[2]) ? $data[2] : null,
            'data2' => isset($data[3]) ? $data[3] : null,
            'batch' => $this->batch,

        ]);
    }

}
