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

    public function __construct(StatusNamesRepositoryInterface $statusNamesRepository, Status $status)
    {
        $this->statusNamesRepository = $statusNamesRepository;
        $this->model = $status;
    }

    public function new($name)
    {
        $statusName = $this->statusNamesRepository->getOrCreateByName($name);
        return $this->model->create(['status_id' => $statusName->id, 'date_bought' => date("Y-m-d H:i:s")]);
    }

    public function updateStatus(Status $status, $name)
    {
        $statusName = $this->statusNamesRepository->getOrCreateByName($name);
        $status->Status()->associate($statusName);
        $status->date_paid = date("Y-m-d H:i:s");
        $status->save();
    }

    public function createFromMKM($data)
    {
        $statusName = $this->statusNamesRepository->getOrCreateByName($data->state);

        return $this->model->firstOrCreate([
            'status_id' => $statusName->id,
            'date_bought' => substr(str_replace('T', ' ', $data->dateBought), 0, 16),
            'date_paid' => isset($data->datePaid) ? substr(str_replace('T', ' ', $data->datePaid), 0, 16) : null,
            'date_sent' => isset($data->dateSent) ? substr(str_replace('T', ' ', $data->dateSent), 0, 16) : null,
            'date_received' => isset($data->dateReceived) ? substr(str_replace('T', ' ', $data->dateReceived), 0, 16) : null,
            'date_canceled' => isset($data->dateCanceled) ? substr(str_replace('T', ' ', $data->dateCanceled), 0, 16) : null,
            'reason' => isset($data->reason) ? $data->reason : null,
            'was_merged_into' => isset($data->wasMergedInto) ? $data->wasMergedInto : null,
        ]);
    }

    public function updateStatusMKM(Status $status, $data)
    {
        $statusName = $this->statusNamesRepository->getOrCreateByName($data->state);
        $status->Status()->associate($statusName);

        if (isset($data->datePaid) && $data->datePaid != $status->date_paid) {
            $status->date_paid = substr(str_replace('T', ' ', $data->datePaid), 0, 16);
        }
        if (isset($data->dateSent) && $data->dateSent != $status->date_sent) {
            $status->date_sent = substr(str_replace('T', ' ', $data->dateSent), 0, 16);
        }
        if (isset($data->dateReceived) && $data->dateReceived != $status->date_received) {
            $status->date_received = substr(str_replace('T', ' ', $data->dateReceived), 0, 16);
        }
        if (isset($data->dateCanceled) && $data->dateCanceled != $status->date_canceled) {
            $status->date_canceled = substr(str_replace('T', ' ', $data->dateCanceled), 0, 16);
        }
        if (isset($data->reason) && $data->reason != $status->reason) {
            $status->reason = $data->reason;
        }
        if (isset($data->wasMergedInto) && $data->wasMergedInto != $status->was_merged_into) {
            $status->was_merged_into = $data->wasMergedInto;
        }
        $status->save();

        return $status;
    }

}
