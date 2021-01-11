<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 21/03/2019
 * Time: 16:59
 */

namespace App\Repositories;

use App\Http\Requests\ItemAddRequest;
use App\Models\Evaluation;
use \App\Models\Item;
use \App\Models\Command;
use App\Models\ShippingMethod;
use App\Models\Status;
use App\Models\StatusName;
use App\Services\StockService;
use http\Env\Request;

class EvaluationRepository extends ModelRepository implements EvaluationRepositoryInterface
{
    private $complaintRepository;

    public function __construct(ComplaintRepositoryInterface $complaintRepository, Evaluation $evaluation)
    {
        $this->complaintRepository = $complaintRepository;
        $this->model = $evaluation;
    }

    public function createFromMKM($data){

        $this->model =  $this->model->firstOrCreate(
                [
                    'evaluation_grade' => $data->evaluationGrade,
                    'item_description' => $data->itemDescription,
                    'packaging' => $data->packaging,
                    'speed' => isset($data->speed) ? $data->speed : null,
                    'comment' => $data->comment,
                ]
            );
        if (isset($data->evaluation->complaint))
            foreach ($data->complaint as $complaint) {
                $this->model->Complaints()->attach($this->complaintRepository->getOrCreateByName($data));
            }
        return $this->model;
    }

}
