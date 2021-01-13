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
use App\Models\Address;
use App\Models\Command;
use App\Models\Complaint;
use App\Models\Evaluation;
use App\Models\Method;
use App\Models\ShippingMethod;
use \App\Models\Status;
use App\Models\StatusName;
use App\Models\User;
use App\Services\MKMService;
use http\Client\Request;
use Illuminate\Support\Facades\Hash;

class CommandRepository extends ModelRepository implements CommandRepositoryInterface
{

    protected $paymentRepository;
    protected $itemRepository;
    protected $usersRepository;
    protected $statusRepository;
    protected $shippingMethodRepository;
    protected $evaluationRepository;
    protected $addressRepository;
    protected $statusNamesRepository;
    protected $MKMService;

    private $nbrPerPage = 25;

    public function __construct(
        Command $command,
        PaymentRepositoryInterface $paymentRepository,
        ItemRepositoryInterface $itemRepository,
        UserRepositoryInterface $usersRepository,
        StatusRepositoryInterface $statusRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        EvaluationRepositoryInterface $evaluationRepository,
        AddressRepositoryInterface $addressRepository,
        StatusNamesRepositoryInterface $statusNamesRepository,
        MKMService $MKMService
    )
    {
        $this->model = $command;
        $this->paymentRepository = $paymentRepository;
        $this->itemRepository = $itemRepository;
        $this->usersRepository = $usersRepository;
        $this->statusRepository = $statusRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->evaluationRepository = $evaluationRepository;
        $this->addressRepository = $addressRepository;
        $this->statusNamesRepository = $statusNamesRepository;
        $this->MKMService = $MKMService;
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

    public function getByType($type, $onlyMKM = false)
    {
        if (!is_numeric($type))
            if ($this->statusNamesRepository->getByType($type != null))
                $type = $this->statusNamesRepository->getByType($type)->id;

        if ($type != 0 && $type != null)
            $commands = $this->model->with('status')
                ->whereHas('status', function ($q) use ($type) {
                    return $q->where('status_id', '=', $type);
                });
        else
            $commands = $this->model;

        if ($onlyMKM) {
            $commands = $commands->whereNotNull('idOrderMKM');
        }

        return $commands->get();
    }

    public function createFromMKM($data, $dateStock)
    {
        $this->model = new Command();
        $seller = $this->usersRepository->firstOrCreateFromMKM($data->seller);
        $buyer = $this->usersRepository->firstOrCreateFromMKM($data->buyer);

        $status = $this->statusRepository->createFromMKM($data->state);

        $shippingMethod = $this->shippingMethodRepository->createFromMKM($data->shippingMethod);

        $shippingAddress = $this->getShippingAddress($data, $buyer);


        $this->model->idOrderMKM = $data->idOrder;
        $this->model->tracking_number = isset($data->trackingNumber) ? $data->trackingNumber : null;
        $this->model->temporary_email = isset($data->temporaryEmail) ? $data->temporaryEmail : null;
        $this->model->is_presale = isset($data->isPresale) ? $data->isPresale : null;
        $this->model->article_value = $data->articleValue;
        $this->model->total_value = $data->totalValue;


        $this->model->storekeeper()->associate($seller);
        $this->model->client()->associate($buyer);
        $this->model->status()->associate($status);
        $this->model->billing_address()->associate($buyer->address);
        $this->model->delivery_address()->associate($shippingAddress != null ? $shippingAddress : $buyer->address);
        $this->model->shippingMethod()->associate($shippingMethod);
        if (isset($data->evaluation)) {
            $evaluation = $this->evaluationRepository->createFromMKM($data->evaluation);
            $this->model->Evaluation()->associate($evaluation);
        }
        $this->model->save();

        $items = $this->itemRepository->storeFromMKM($data->article, $this->model, strtotime($this->model->status->date_bought) > $dateStock);
        return $this->model;
    }

    public function getByIdMKM($id)
    {
        return $this->model->where('idOrderMKM', '=', $id)->first();
    }

    public function setTrackingNumber($id, $trackingNumber)
    {
        $this->model = $this->model->find($id);
        $this->MKMService->setTrackingNumber($this->model->idOrderMKM, $trackingNumber);
        $this->model->tracking_number = $trackingNumber;
        $this->model->save();
        return $this->model;
    }

    public function checkStatus($id, $data)
    {
        $changed = false;
        $command = $this->getById($id);
        if ($command->status->StatusName() != $data->state->state) {
            $status = $this->statusRepository->updateStatusMKM($command->status, $data->state);

            if ($status->StatusName() == "paid") {
                $shippingAddress = $this->getShippingAddress($data, $command->buyer);
                $command->delivery_address()->associate($shippingAddress != null ? $shippingAddress : $command->buyer->address);
            } elseif ($status->StatusName() == "evaluated") ;
            if (isset($data->evaluation)) {
                $evaluation = $this->evaluationRepository->createFromMKM($data->evaluation);
                $command->evaluation()->associate($evaluation);
            }
            $changed = true;
        }
        if (isset($data->trackingNumber) && $data->trackingNumber != "" && $data->trackingNumber != $command->tracking_number) {
            $command->tracking_number = $data->trackingNumber;
            $changed = true;
        }
        if (isset($data->temporaryEmail) && $data->temporaryEmail != "" && $data->temporaryEmail != $command->temporary_email) {
            $command->temporary_email = $data->temporaryEmail;
            $changed = true;
        }
        if ($changed) {
            $command->save();
            return true;
        }
        return false;
    }

    private function getShippingAddress($data, $buyer)
    {
        $shippingAddress = null;
        if (
            $data->shippingAddress->name != '' ||
            $data->shippingAddress->extra != '' ||
            $data->shippingAddress->street != '' ||
            $data->shippingAddress->zip != '' ||
            $data->shippingAddress->city != '' ||
            $data->shippingAddress->country != ''
        ) {
            $shippingAddress = $buyer->Addresses()->save(
                $this->addressRepository->createFromMKM($data->shippingAddress)
            );
        }
        return $shippingAddress;
    }

    public function setSend($id)
    {
        $this->model = $this->getById($id);
        if ($this->model->idOrderMKM != null) {
            $answer = $this->MKMService->changeState($this->model->idOrderMKM, MKMService::Send);
            \Debugbar::info($answer);
            if (isset($answer->order)) {
                $this->model = $this->checkStatus($id, $answer->order);
                return $this->model;
            } else return false;
        }

        return $this->model->setSent();
    }

    public function acceptCancellation($id, $relistItems)
    {
        $this->model = $this->getById($id);

        if ($this->model->idOrderMKM != null) {
            $answer = $this->MKMService->changeState($this->model->idOrderMKM, MKMService::AcceptCancellation);

            if (!isset($answer->order)) {
                return false;
            }
            $this->model = $this->checkStatus($id, $answer->order);
        } else
            $this->model->setCanceled();

        if ($relistItems)
            $this->itemRepository->relistItems($this->model->items);

        return $this->model;
    }

}
