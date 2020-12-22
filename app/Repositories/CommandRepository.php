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
use App\Models\Command;
use \App\Models\Status;
use http\Client\Request;

class CommandRepository extends ModelRepository implements CommandRepositoryInterface
{

    protected $paymentRepository;
    protected $itemRepository;

    private $nbrPerPage = 25;

    public function __construct(
        Command $command,
        PaymentRepositoryInterface $paymentRepository,
        ItemRepositoryInterface $itemRepository)
    {
        $this->model = $command;
        $this->paymentRepository = $paymentRepository;
        $this->itemRepository = $itemRepository;
    }

    public function newCart($user)
    {
        $payment = $this->paymentRepository->new();
        $payment->save();

        $cart = new $this->model;

        $cart->client_id = $user->id;
        $cart->storekeeper_id = 1;
        $cart->payment_id = $payment->id;
        $cart->status_id = 1;

        $cart->save();
        return $cart;
    }

    public function getById($id)
    {
        return $this->model->where('id', $id)->with('items')->first();
    }

    public function getByUser($user)
    {
        return $this->model
            ->where('status_id', '<>', 1)
            ->where('status_id', '<>', 3)
            ->where('client_id', $user->id)
            ->get();
    }

    public function getWantByUser($user)
    {
        return $this->model->where('status_id', 3)->where('client_id', $user->id)->get();
    }

    public function getWantById($id)
    {
        return $this->model->where('status_id', 3)->where('id', $id)->first();
    }

    public function getCartByUser($user)
    {
        $cart = $this->model->where('status_id', 1)->where('client_id', $user->id)->with('items', 'payment')->first();
        if ($cart == null)
            $cart = $this->newCart($user);

        return $cart;
    }

    public function addItemToCart(Request $request)
    {
        $cart = $this->getCartByUser($request->user());

        $i = $this->itemRepository->stores($request, $cart);
        return $i;
    }

    public function addItemToWant(Request $request)
    {
        $cart = $this->getWantByUser($request->user());

        $i = $this->itemRepository->stores($request, $cart);
        return $i;
    }

    public function removeItemFromCart(Request $request)
    {
        $this->itemRepository->decrease($request->id, $request->quantity);

        return true;
    }

    public function removeItemFromWant(Request $request)
    {
        $item = $this->itemRepository->decrease($request->id, $request->quantity);

        return;
    }

    public function want(Request $request)
    {
        $command = $this->getCartByUser($request->user());

        //do payement
        $payment = $command->payment;
        //$payment->type = $inputs['payment'];
        $payment->amount = $command->amount();
        $payment->save();

        $command->status_id = Status::want();

        $command->save();

        return $command;
    }


    public function confirm(Request $request)
    {
        $command = $this->getCartByUser($request->user());
        if (count($command->items) == 0)
            return false;
        $command->delivery_address_id = $request->address != 0 ? $request->address : null;
        if (isset($request->billing_address_chb))
            $command->billing_address_id = $request->billing_address != 0 ? $request->address : null;
        else
            $command->billing_address_id = $request->address != 0 ? $request->address : $request->user()->address_id;

        //do payement
        $payment = $command->payment;
        //var_dump($inputs);
        $payment->type = $request->payment;
        $payment->amount = $command->amount();
        $payment->save();

        $command->status_id = Status::confirmed();

        $command->save();


        return $command;
    }


    public function getCommandsPaginate($type)
    {
        if ($type != 0)
            $commands = $this->model->with('status')->whereHas('status', function ($q) use ($type) {
                return $q->where('status_id', '=', $type);
            })->paginate($this->nbrPerPage);
        else
            $commands = $this->model->paginate($this->nbrPerPage);
        return $commands;
    }
}
