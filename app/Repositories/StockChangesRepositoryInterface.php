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

interface StockChangesRepositoryInterface extends ModelRepositoryInterface
{
    public function add($type, $data);
}
