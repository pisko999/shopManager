<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 21/03/2019
 * Time: 16:58
 */

namespace App\Repositories;


use App\Http\Requests\ItemAddRequest;
use App\Models\Command;
use App\Models\Status;
use http\Env\Request;

interface StatusRepositoryInterface extends ModelRepositoryInterface
{
    public function new($name);
    public function updateStatus(Status $status,$name);
    public function createFromMKM($data);
    public function updateStatusMKM(Status $status,$data);
}
