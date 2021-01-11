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
use http\Env\Request;

interface ItemRepositoryInterface
{
    public function stores(Request $request, Command $command);

    public function increase($id, $quantity);

    public function decrease($id, $quantity);

    public function storeFromMKM($data, $command, $updateStock);
    public function relistItems($items);
    }
