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
use App\Models\Gift;
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
    protected $giftItemsRepository;
    protected $MKMService;

    private $nbrPerPage = 50;

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
        GiftItemRepositoryInterface $giftItemsRepository,
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
        $this->giftItemsRepository = $giftItemsRepository;
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
        return $this->model->where('id', $id)->with('items', 'items.stock', 'items.stock.product', 'items.stock.product.expansion', 'items.stock.product.image')->first();
    }

    public function getByIds($ids)
    {
        return $this->model->whereIn('id', $ids)->with('items')->with('status')->with('gifts', 'gifts.giftItems', 'gifts.giftItems.product')->get()->sortBy('status.date_paid');
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

    public function getCommandsPaginate($type, $presale = false)
    {
        $orderByDate = match($type) {
            // 1 => 'date_bought',
            '2' => 'date_paid',
            '3', '5' => 'date_sent',
            '4', '12' => 'date_received',
            6 => 'date_canceled',
            default => 'date_bought'
        };
        $direction = $type == 2 ? 'asc' : 'desc';
        if ($type != 0) {
            $commands = $this->model
                ->with('status', 'items', 'gifts', 'status.status','status.name', 'client','billing_address', 'shippingMethod', 'shippingMethod.method')
                ->where('is_presale', '=', $presale)
                ->whereHas('status', function ($q) use ($type) {
                    return $q->where('status_id', '=', $type);
                })
                ->orderBy(Status::select($orderByDate)->whereColumn('statuses.id', 'commands.status_id'), $direction)
                ->paginate($this->nbrPerPage);
        } else{
            $commands = $this->model->paginate($this->nbrPerPage)->sortBy('status.date_paid');
            }
        return $commands;
    }

    public function getByType($type, $onlyMKM = false, $onlyPresale = false)
    {
        if (!is_numeric($type))
            if ($this->statusNamesRepository->getByType($type) != null)
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
        if (!$onlyPresale) {
            $commands = $commands->where('is_presale', '=', 0);
        }
        if($type == 2){
            return $commands->get()->sortBy('status.date_paid');
        }else {
            return $commands->get()
                ;
        }
    }

    public function createFromMKM($data, $dateStock)
    {
	$no = date('ym', time()) . '001';
	$max = Command::max('invoice_no');
	if($max > $no) {
	    $no = $max + 1;
	}
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
	$this->model->invoice_no = $no;

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
//        $address = $this->addressRepository->createFromMKM($command->client->id, $data->shippingAddress);
        $address = $this->getShippingAddress($data, $command->client);
        if($address && ($address->wasRecentlyCreated || $command->delivery_address != $address)){
            $command->delivery_address()->associate($address);
            echo "Address changed\n";
            $changed = true;
        }
        if ($changed) {
            $command->save();
            return true;
        }
        return false;
    }

    private function checkAddress($data){

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
            $shippingAddress = $this->addressRepository->createFromMKM($buyer->id, $data->shippingAddress);
	    if ($shippingAddress != null && $shippingAddress->wasRecentlyCreated) {
		echo "buyer: " . $buyer->name . " / address: " . $data->shippingAddress->street . "\n";
		$buyer->Addresses()->save($shippingAddress);
	    }
        }
        return $shippingAddress;
    }

    public function setSend($id)
    {
        $command = $this->getById($id);
        if(!$command)
            return false;
        if ($command->idOrderMKM != null) {
            $answer = $this->MKMService->changeState($command->idOrderMKM, MKMService::Send);
            if (isset($answer->order)) {
                $command = $this->checkStatus($id, $answer->order);
                return $command;
            } else
                return false;
;
        }
        if(!$command->delivery_address)
            if($command->status->setSold())
                return $command;
        return $command->setSent();
    }

    public function setPaid($id){
        $command = $this->getById($id);
        if(!$command)
            return false;
        if(!$command->delivery_address)
            return $command->status->setSold();
        return $command->status->setPaid();
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

    public function checkByIdMKM($id)
    {
        $command = $this->getByIdMKM($id);
        if($command)
            return $command;

        $command = $this->MKMService->getOrder($id);
        if(!isset($command->order))
            return false;
        $dateStock = \Storage::lastModified('MKMResponses/stockFile.csv');

        $command = $this->createFromMKM($command->order, $dateStock);
        return $command;
    }

    public function addGift($id, $count)
    {
        $command = $this->getById($id);
        if (!$command || $command->gifts->count()) {
            return false;
        }

        $gift = new Gift();
        $gift->Command()->associate($command);
        $gift->save();

        $giftItems = $this->giftItemsRepository->getRandomGifts(2,$count);
        foreach($giftItems as $giftItem) {
            $giftItem->gifts()->attach($gift,['quantity' => 1]);
        }
        return count($giftItems);
    }
    public function getSoldByMonth($month, $year){
        return $this->model
            ->whereHas('status', function ($q) use ($month, $year){
                $q->whereRaw('MONTH(date_paid) = ' . $month)->whereRaw('YEAR(date_paid) = ' . $year);
//            })
//            ->whereHas('items', function($q) {
//                $q->whereHas('stock', function ($q) {
//                    $q->whereHas('buyItems');
////                    $q->whereDoesntHave('buyItems');
//                });
            })->with('items','items.stock', 'items.stock.buyItems', 'items.stock.product','status','buyer', 'billing_address')
            ->orderBy(Status::select('date_paid')->whereColumn('statuses.id', 'commands.status_id'));
            ;
    }

    public function getBoughtInstoreByMonth($month, $year)
    {
        return $this->model
            ->whereHas('status', function ($q) use ($month, $year){
                $q->whereRaw('MONTH(date_bought) = ' . $month)->whereRaw('YEAR(date_bought) = ' . $year);
            })->whereNull('idOrderMKM')->with('items')
            ->orderBy(Status::select('date_paid')->whereColumn('statuses.id', 'commands.status_id'));
        ;
    }
}
