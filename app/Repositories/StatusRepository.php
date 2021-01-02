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
use App\Models\Status;
use App\Models\StatusName;
use App\Services\StockService;
use http\Env\Request;

class StatusRepository extends ModelRepository implements StatusRepositoryInterface
{
    private $statusNamesRepository;

    public function __construct(StatusNamesRepositoryInterface $statusNamesRepository,Status $status)
    {
        $this->statusNamesRepository = $statusNamesRepository;
        $this->model = $status;
    }

    public function new($name){
        $statusName = $this->statusNamesRepository->getOrCreateByName($name);
        return $this->model->create(['status_id'=>$statusName->id, 'date_bought' => date("Y-m-d H:i:s")]);
    }

    public function updateStatus(Status $status,$name){
        $statusName = $this->statusNamesRepository->getOrCreateByName($name);
        $status->Status()->associate($statusName);
        $status->date_paid = date("Y-m-d H:i:s");
        $status->save();
    }

}
