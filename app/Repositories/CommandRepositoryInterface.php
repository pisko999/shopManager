<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 21/03/2019
 * Time: 15:37
 */

namespace App\Repositories;


use App\Http\Requests\CartConfirmRequest;
use App\Http\Requests\ItemAddRequest;
use App\Http\Requests\ItemRemoveRequest;
use App\Http\Requests\WantConfirmRequest;
use http\Client\Request;

interface CommandRepositoryInterface
{
    public function newCart($user);

    public function getById($id);

    public function getByUser($id);

    public function getWantByUser($user);

    public function getWantById($id);

    public function getCartByUser($user);

    public function addItemToCart(Request $request);

    public function addItemToWant(Request $request);

    public function removeItemFromCart(Request $request);

    public function removeItemFromWant(Request $request);

    public function want(Request $request);

    public function confirm(Request $request);

    public function getCommandsPaginate($type);

    public function createFromMKM($data, $dateStock);

    public function getByIdMKM($id);

    public function getByType($type, $onlyMKM = false);

    public function checkStatus($id, $data);

    public function setSend($id);
    public function acceptCancellation($id, $relistItems);
    public function setTrackingNumber($id,$trackingNumber);

}
