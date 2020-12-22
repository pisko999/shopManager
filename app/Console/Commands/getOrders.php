<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Complaint;
use App\Models\Evaluation;
use App\Models\Item;
use App\Models\Method;
use App\Models\ShippingMethod;
use App\Models\Status;
use App\Models\StatusName;
use App\Models\Stock;
use App\Models\User;
use App\Services\MKMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;


class getOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getOrders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'getting orders from MKM';

    private $MKMService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MKMService $MKMService)
    {
        $this->MKMService = $MKMService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //settings
        $new = true;




        $orders = $this->MKMService->getSellerOrders("paid");
//var_dump($orders);
        if (!isset($orders->order))
            return -1;

        foreach ($orders->order as $order) {
            //var_dump($order->seller->name);
            $command = \App\Models\Command::where('idOrderMKM', '=', $order->idOrder)->get();
            //var_dump($command);
            if (count($command) == 0) {
                $seller = User::firstOrCreate([
                    'mkm_id' => $order->seller->idUser,
                ], [
                    'mkm_username' => $order->seller->username,
                    'mkm_country' => isset($order->seller->country) ? $order->seller->country : null,
                    'mkm_is_commercial' => $order->seller->isCommercial,
                    'mkm_reputation' => $order->seller->riskGroup,
                    'mkm_risk_group' => $order->seller->reputation,
                    'mkm_ships_fast' => $order->seller->shipsFast,
                    'mkm_sell_count' => $order->seller->sellCount,
                    'name' => isset($order->seller->name) && isset($order->seller->name->lastName) ? $order->seller->name->lastName : $order->seller->username,
                    'forename' => isset($order->seller->name) && isset($order->seller->name->firstName) ? $order->seller->name->firstName : null,
                    'email' => $order->seller->idUser . '@mkm.com',
                    'password' => Hash::make($order->seller->username),
                ]);

                //var_dump($order->seller->address);
                $sellerAddress = Address::where([
                    'name' => $order->seller->address->name,
                    'extra' => $order->seller->address->extra,
                    'street' => $order->seller->address->street,
                    'postal' => $order->seller->address->zip,
                    'city' => $order->seller->address->city,
                    'country' => $order->seller->address->country,
                ])->first();

                if ($sellerAddress == null) {
                    $sellerAddress = new Address([
                        'name' => $order->seller->address->name,
                        'extra' => $order->seller->address->extra,
                        'street' => $order->seller->address->street,
                        'postal' => $order->seller->address->zip,
                        'city' => $order->seller->address->city,
                        'country' => $order->seller->address->country,
                    ]);

                    $seller->addresses()->save($sellerAddress);
                }
                //var_dump($order->buyer);

                $buyer = User::firstOrCreate([
                    'mkm_id' => $order->buyer->idUser,
                ], [
                    'mkm_username' => $order->buyer->username,
                    'mkm_country' => isset($order->buyer->country) ? $order->buyer->country : null,
                    'mkm_is_commercial' => $order->buyer->isCommercial,
                    'mkm_reputation' => $order->buyer->riskGroup,
                    'mkm_risk_group' => $order->buyer->reputation,
                    'mkm_ships_fast' => $order->buyer->shipsFast,
                    'mkm_sell_count' => $order->buyer->sellCount,
                    'name' => isset($order->buyer->name) && isset($order->buyer->name->lastName) ? $order->buyer->name->lastName : $order->buyer->username,
                    'forename' => isset($order->buyer->name) && isset($order->buyer->name->firstName) ? $order->buyer->name->firstName : null,
                    'email' => $order->buyer->idUser . '@mkm.com',
                    'password' => Hash::make($order->buyer->username),
                ]);
                //var_dump($buyer);

                $buyerAddress = Address::where([
                    'name' => $order->buyer->address->name,
                    'extra' => $order->buyer->address->extra,
                    'street' => $order->buyer->address->street,
                    'postal' => $order->buyer->address->zip,
                    'city' => $order->buyer->address->city,
                    'country' => $order->buyer->address->country,
                ])->first();

                if ($buyerAddress == null) {
                    $buyerAddress = new Address([
                        'name' => $order->buyer->address->name,
                        'extra' => $order->buyer->address->extra,
                        'street' => $order->buyer->address->street,
                        'postal' => $order->buyer->address->zip,
                        'city' => $order->buyer->address->city,
                        'country' => $order->buyer->address->country,
                    ]);

                    $buyer->addresses()->save($buyerAddress);
                }
                $statusName = StatusName::firstOrCreate(['name' => $order->state->state]);

                $state = Status::firstOrCreate([
                    'status_id' => $statusName->id,
                    'date_bought' => substr(str_replace('T', ' ', $order->state->dateBought), 0, 16),
                    'date_paid' => isset($order->state->datePaid) ? substr(str_replace('T', ' ', $order->state->datePaid), 0, 16) : null,
                    'date_sent' => isset($order->state->dateSent) ? substr(str_replace('T', ' ', $order->state->dateSent), 0, 16) : null,
                    'date_received' => isset($order->state->dateReceived) ? substr(str_replace('T', ' ', $order->state->dateReceived), 0, 16) : null,
                ]);

                $method = Method::firstOrCreate(
                    [
                        'id' => $order->shippingMethod->idShippingMethod,
                        'name' => $order->shippingMethod->name
                    ]
                );

                $shippingMethod = ShippingMethod::firstOrCreate(
                    [
                        'method_id' => $method->id,
                        'price' => $order->shippingMethod->price,
                        'is_letter' => $order->shippingMethod->isLetter,
                        'is_insured' => $order->shippingMethod->isInsured,
                    ]
                );

                if (
                    $order->shippingAddress->name != '' ||
                    $order->shippingAddress->extra != '' ||
                    $order->shippingAddress->street != '' ||
                    $order->shippingAddress->zip != '' ||
                    $order->shippingAddress->city != '' ||
                    $order->shippingAddress->country != ''
                ) {
                    $shippingAddress = Address::where([
                        'name' => $order->shippingAddress->name,
                        'extra' => $order->shippingAddress->extra,
                        'street' => $order->shippingAddress->street,
                        'postal' => $order->shippingAddress->zip,
                        'city' => $order->shippingAddress->city,
                        'country' => $order->shippingAddress->country,
                    ])->first();
                    if ($shippingAddress == null) {
                        $shippingAddress = new Address([
                            'name' => $order->shippingAddress->name,
                            'extra' => $order->shippingAddress->extra,
                            'street' => $order->shippingAddress->street,
                            'postal' => $order->shippingAddress->zip,
                            'city' => $order->shippingAddress->city,
                            'country' => $order->shippingAddress->country,
                        ]);
                        $buyer->addresses()->save($shippingAddress);
                    }
                }
                //var_dump($order->evaluation);
                if (isset($order->evaluation)) {
                    $evaluation = Evaluation::create(
                        [
                            'evaluation_grade' => $order->evaluation->evaluationGrade,
                            'item_description' => $order->evaluation->itemDescription,
                            'packaging' => $order->evaluation->packaging,
                            'speed' => isset($order->evaluation->speed) ? $order->evaluation->speed : null,
                            'comment' => $order->evaluation->comment,
                        ]
                    );
                    if (isset($order->evaluation->complaint))
                        foreach ($order->evaluation->complaint as $complaint) {
                            $compl = Complaint::firstOrCreate(['name' => $complaint]);
                            $evaluation->Complaints()->attach($compl);
                        }
                }

                $MKMcommand = new \App\Models\Command([
                    'idOrderMKM' => $order->idOrder,
                    'tracking_number' => isset($order->trackingNumber) ? $order->trackingNumber : null,
                    'temporary_email' => isset($order->temporaryEmail) ? $order->temporaryEmail : null,
                    'is_presale' => isset($order->isPresale) ? $order->isPresale : null,
                ]);
                $MKMcommand->storekeeper()->associate($seller);
                $MKMcommand->client()->associate($buyer);
                $MKMcommand->status()->associate($state);
                $MKMcommand->billing_address()->associate($buyerAddress);
                $MKMcommand->delivery_address()->associate($buyerAddress);
                $MKMcommand->shippingMethod()->associate($shippingMethod);

                $MKMcommand->save();
                \DB::beginTransaction();
                foreach ($order->article as $article) {
                    $stock = Stock::where('idArticleMKM', $article->idArticle)->first();
                    //var_dump($stock);

                    if ($stock == null) {
                        echo $article->idProduct . ', ' . $article->product->enName . '\r';
                        $stock = new Stock([
                            'all_product_id' => $article->idProduct,
                            'initial_price' => $article->price,
                            'quantity' => 0,
                            'price' => $article->price,
                            'stock' => $article->price > 1.97 ? 3 : ($article->price > 0.97 ? 2 : 1),
                            'language' => $article->language->idLanguage,
                            'isFoil' => isset($article->isFoil) ? $article->isFoil : null,
                            'signed' => isset($article->isSigned) ? $article->isSigned : null,
                            'playset' => isset($article->isPlayset) ? $article->isPlayset : null,
                            'altered' => isset($article->isAltered) ? $article->isAltered : null,
                            'state' => isset($article->condition) ? $article->condition : "NM",
                            'comments' => $article->comments,
                            'idArticleMKM' => $article->idArticle,
                            'modifiedMKM' => isset($article->lastEdited) ? $article->lastEdited : null,
                        ]);
                        $stock->save();
                        //continue;
                    }
                    //var_dump($article);
                    $item = new Item([
                        'stock_id' => $stock->id,
                        'command_id' => $MKMcommand->id,
                        'price' => $article->price,
                        'quantity' => $article->count,
                    ]);
                    $item->save();
                    if($new) {
                        $stock->quantity -= $article->count;
                        $stock->save();
                    }
                }

                \DB::commit();

            }
        }
        return 0;
    }
}
