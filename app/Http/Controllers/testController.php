<?php

namespace App\Http\Controllers;

use App\Libraries\PriceLibrary;
use App\Models\Address;
use App\Models\BuyCommand;
use App\Models\BuyItem;
use App\Models\BuyItemItem;
use App\Models\Command;
use App\Models\Expansion;
use App\Models\Status;
use App\Models\Stock;
use App\Models\StockChange;
use App\Models\User;
use App\Objects\FPDFO;
use App\Objects\pdfAddress;
use App\Objects\pdfFacture;
use App\Objects\StockFileItem;
use App\Objects\XmlPohoda;
use App\Repositories\BuyCommandRepositoryInterface;
use App\Repositories\BuyItemRepositoryInterface;
use App\Repositories\CommandRepositoryInterface;
use App\Repositories\ExpansionRepositoryInterface;
use App\Repositories\GiftItemRepository;
use App\Repositories\GiftItemRepositoryInterface;
use App\Repositories\StatusNamesRepositoryInterface;
use App\Repositories\StatusRepositoryInterface;
use App\Repositories\StockRepositoryInterface;
use App\Services\MKMService;
use App\Services\StockService;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;

class testController extends Controller
{
    private $stockRepository;
    private $statusRepository;
    private $MKMService;
    private $stockService;
    private $buyCommandRepository;
    private $buyItemRepository;
    private $commandRepository;
    private $expansionRepository;
    private $giftItemRepository;

    public function __construct(
        StockRepositoryInterface $stockRepository,
        StatusRepositoryInterface $statusRepository,
        MKMService $MKMService,
        StockService $stockService,
        BuyCommandRepositoryInterface $buyCommandRepository,
        BuyItemRepositoryInterface $buyItemRepository,
        GiftItemRepositoryInterface $giftItemRepository,
        CommandRepositoryInterface $commandRepository,
        ExpansionRepositoryInterface $expansionRepository
    )
    {
        $this->stockRepository = $stockRepository;
        $this->statusRepository = $statusRepository;
        $this->MKMService = $MKMService;
        $this->stockService = $stockService;
        $this->buyCommandRepository = $buyCommandRepository;
        $this->buyItemRepository = $buyItemRepository;
        $this->giftItemRepository = $giftItemRepository;
        $this->commandRepository = $commandRepository;
        $this->expansionRepository = $expansionRepository;
    }

    private function setBuyItems($month, $year) {
        $newBuyCommands = $this->createRebuys($month, $year);
        $buyCommands = $this->buyCommandRepository->getBoughtFromSroBeginning($month, $year)->get();
//        \Debugbar::info($buyCommands);
        $items = collect();
        foreach ($buyCommands as $buyCommand) {
            if (!empty($buyCommand->items)) {
                foreach($buyCommand->items as $item) {
                    $items[$item->id] = $item;
                }
            }
        }
        $tbStocks = collect();
        $created = 0;
        $commands =  $this->commandRepository->getSoldByMonth($month, $year)->get();
//        \Debugbar::info($commands->first()->items()->first());
//        return;
        foreach ($commands as $command) {
            foreach ($command->items as $item) {
                $quantity = $item->quantity;
                if (!$item->stock) {
                    $stock = new Stock();
                    $buyItem = $this->buyItemRepository->getByIdStock($item->stock_id)->first();
                    $stock->id = $item->stock_id;
                    $stock->all_product_id = $buyItem->id_product;
                    $stock->initial_price = $buyItem->price;
                    $stock->quantity = $item->quantity;
                    $stock->price = $item->price;
                    $stock->language_id = $buyItem->id;
                    $stock->isFoil = $buyItem->isFoil;
                    $stock->signed = $buyItem->signed;
                    $stock->altered = $buyItem->altered;
                    $stock->playset = $buyItem->playset;
                    $stock->state = $buyItem->state;
                    $stock->save();
                    $item->stock()->associate($stock);
                }
                if ($item->stock->buyItems) {
                    foreach ($item->stock->buyItems as $buyItem) {
                        if (in_array($buyItem->id, $items->keys()->all())) {
                            $actBuyItem = $items->get($buyItem->id);
                            $restQuantity = $actBuyItem->quantity - $actBuyItem->sold_quantity;
                            if ($restQuantity < 1) {
                                continue;
                            }
                            $buyItemItem = new BuyItemItem();
                            $buyItemItem->item()->associate($item);
                            $buyItemItem->buyItem()->associate(($actBuyItem));

                            if ($restQuantity < $quantity) {
                                $actBuyItem->sold_quantity = $actBuyItem->quantity;
                                $buyItemItem->quantity = $restQuantity;
                                $quantity -= $restQuantity;
                            } else {
                                $actBuyItem->sold_quantity += $quantity;
                                $buyItemItem->quantity = $quantity;
                                $quantity = 0;
                            }
                            try {
                                $actBuyItem->save();
                                $buyItemItem->save();
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
                if ($quantity) {
//                    \Debugbar::info($item);
//                    \Debugbar::info("nesmi sem dojit");
//                    return;
                    $created++;
                    $buyItem = new BuyItem();
                    $buyItem->id_product = $item->stock->all_product_id;
                    $buyItem->id_stock = $item->stock->id;
                    $buyItem->id_language = $item->stock->language_id;
                    $buyItem->price = round($item->price * 0.85, 2);
                    $buyItem->quantity = $quantity;
                    $buyItem->state = $item->stock->state;
                    $buyItem->isFoil = $item->stock->isFoil;
                    $buyItem->playset = $item->stock->playset;
                    $buyItem->signed = $item->stock->signed;
                    $buyItem->altered = $item->stock->altered;
                    $buyItem->added = 1;
                    $buyItem->sold_quantity = $quantity;
                    $usedBuyCommand = $newBuyCommands->get(rand(0,$newBuyCommands->count() - 1));
//                    \Debugbar::info($usedBuyCommand);
//                    return;
                    $usedBuyCommand->items()->save($buyItem);

                    $buyItemItem = new BuyItemItem();
                    $buyItemItem->Item()->associate($item);
                    $buyItemItem->buyItem()->associate(($buyItem));
                    $buyItemItem->quantity = $quantity;
                    $buyItemItem->save();
                }
            }
        }
        \Debugbar::info($created);
    }

    private function setRebuyNos($month, $year){
        $buyCommands = $this->buyCommandRepository->getBoughtByMonth($month,$year)->get();
        $i = 0;
        foreach($buyCommands as $buyCommand) {
            if (in_array($buyCommand->client->id, [2985,4313])) {
                continue;
            }
            $i++;
            $buyCommand->document_no = substr($year,2,2) . $month . str_pad($i, 3, "0", STR_PAD_LEFT);
            if ($buyCommand->initial_value == 0) {
                $buyCommand->initial_value = $buyCommand->items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });
                $buyCommand->value = $buyCommand->items->sum(function ($item) {
                    if(!$item->stock)
                        \Debugbar::info($item->id_stock);
                    return $item->quantity * $item->stock->price;
                });
            }
            $buyCommand->save();
        }
    }

    private function getPohodaRebuys($month,$year) {

        $buyCommands = $this->buyCommandRepository->getBoughtByMonth($month,$year)->get();

        $xmlPohoda = new XmlPohoda(new \DOMDocument());
        $xmlPohoda->initRebuys();

        foreach($buyCommands as $buyCommand) {
            if (in_array($buyCommand->client->id, [2985,4313])) {
                continue;
            }
            $xmlPohoda->addRebuy($buyCommand);
        }
        echo $xmlPohoda->getXml("export-vykup-" . $year . "-" . $month . ".xml");
    }

    private function getPohodaCommands($month,$year)
    {
        $commands =  $this->commandRepository->getSoldByMonth($month, $year)->get();

        $xmlPohoda = new XmlPohoda(new \DOMDocument());
        $xmlPohoda->initCommands();
        foreach($commands as $command) {
            $xmlPohoda->addCommand($command);
        }
        echo ($xmlPohoda->getXml("export-prodej-" . $year . "-" . $month . ".xml"));
    }

    private function getPohodaIntern($month,$year)
    {
        $commands =  $this->commandRepository->getSoldByMonth($month, $year)->get();
$amount = 0;
        $xmlPohoda = new XmlPohoda(new \DOMDocument());
        $xmlPohoda->initIntern();
        foreach($commands as $command) {
            $xmlPohoda->addIntern($command, $amount);
        }
        \Debugbar::info($amount);
        echo ($xmlPohoda->getXml("export-intern-" . $year . "-" . $month . ".xml"));
    }

    private function setInvoiceNos($month, $year) {

        $commands =  $this->commandRepository->getSoldByMonth($month, $year)->get();
        $i = 0;
        foreach($commands as $command) {
            $i++;
            $command->invoice_no = substr($year,2) . str_pad($month,2,'0', STR_PAD_LEFT) . str_pad($i, 3, "0", STR_PAD_LEFT);
            $command->save();
        }
    }

    private function makeTotalValue($month, $year) {
        $commands = $this->commandRepository->getBoughtInstoreByMonth($month, $year)->get();
        \Debugbar::info($commands);
        foreach($commands as $command) {
            foreach ($command->items as $item) {
//            $item->price *= 1/25;
//            $item->save();
                $command->total_value += $item->quantity * $item->price;
            }
            \Debugbar::info($command->total_value);
            $command->save();
        }
    }

    private function createRebuys($month, $year) {
        $buyCommands = BuyCommand::where('document_no', '=', 'false')->get();
        if ($buyCommands->count()){
            return $buyCommands;
        }
        $users = User::whereIn('email', ['janpopela@mtgforfun.cz', 'filipdoskocil@mtgforfun.cz', 'filiplukesf@mtgforfun.cz', 'ondrejlikesf@mtgforfun.cz', 'pavelbednar@mtgforfun.cz'])->get();
        $dates = [];
        for($i = 0; $i < $users->count(); $i++) {
            $dates[] = rand(1, cal_days_in_month(CAL_GREGORIAN,$month,$year));
        }
        sort($dates);
        $i = 0;
        $buyCommands = collect();
        \Debugbar::info($buyCommands);
        foreach($users as $user) {
            $date = Carbon::create($year, $month, $dates[$i]);
            $i++;
            \Debugbar::info($user->name);
            $status = new Status();
            $status->status_id = 10;
            $status->date_bought = $date;
            $status->date_paid = $date;
            $status->save();
            $buyCommand = new BuyCommand();
            $buyCommand->document_no = "false";
            $user->buyCommands()->save($buyCommand);
            $buyCommand->save();
            $buyCommand->status()->associate($status)->save();
            $buyCommands->push($buyCommands);
        }
        return $buyCommands;
    }

    public function repairRelation($month, $year){
        $commands =  $this->commandRepository->getSoldByMonth($month, $year)->get();
        foreach($commands as $command) {
            foreach($command->items as $item) {
//                if ($command->id != 4007) {
//                    continue;
//                }
                if ($item->buyItems->count() != 1) {
//                    \Debugbar::info("item");
//                    \Debugbar::info($item->quantity);
                    $quantity = $item->quantity;
                    foreach($item->buyItems as $k => $buyItem) {
                        if ($k == 0 || $quantity > $buyItem->pivot->quantity) {
                            $quantity -= $buyItem->pivot->quantity;
                        } else {
                            if ($quantity == 0) {
                                $buyItem->sold_quantity -= $buyItem->pivot->quantity;
                                $buyItem->save();
                                $item->BuyItems()->detach($buyItem->id);
                            } else {
                                $buyItem->sold_quantity -= ($buyItem->pivot->quantity - $quantity);
                                $buyItem->save();
                                $buyItem->pivot->quantity = $quantity;
                                $buyItem->pivot->save();
                                $quantity = 0;
                            }
                        }
//                        \Debugbar::info($k);
//                        \Debugbar::info($buyItem->sold_quantity);
//                        \Debugbar::info($buyItem->pivot->quantity);
                    }

                }
            }
        }
    }

    public function showOrders($month, $year) {
        $commands =  $this->buyCommandRepository->getBoughtByMonth($month, $year)->get();
        return view('export.showOrders', compact('commands'));

    }
    private function deleteStockByIdExpansion($idExpansion) {
        $expansion = $this->expansionRepository->getByMKMId($idExpansion);
        foreach($expansion->allCards as $product) {
            foreach($product->stock as $stock) {
                if ($stock->quantity) {
                    $quantity = $stock->quantity;
                    foreach($stock->buyItems as $buyItem) {
                        if ($quantity > $buyItem->quantity) {
                            $quantity -= $buyItem->quantity;
                            $buyItem->delete();
                        } elseif ($quantity == $buyItem->quantity){
                            $quantity = 0;
                            $buyItem->delete();
                        } else {
                            $quantity -= $stock->quantity;
                            $buyItem->save();
                            $stock->quantity = 0;
                        }
                        if($stock->quantity == 0) {
                            break;
                        }
                    }
                    $this->stockService->decrease($stock,$stock->quantity);
                }
            }
        }
    }


    public function test()
    {
//        \Debugbar::info($this->MKMService->savePriceGuideList());
//        \Debugbar::info($this->MKMService->saveStockFile());

//        $this->deleteStockByIdExpansion(5359);
//        try {
            $month = 11;
            $year = 2023;
//        $commands = Command::whereNotNull('invoice_no')->get();
//        foreach($commands as $command) {
//            $command->invoice_no = substr($command->invoice_no, 1);
//            $command->save();
//        }
//        $test='';
//        return $this->showOrders($month,$year);
//        $this->makeTotalValue($month, $year);
//        ini_set('memory_limit', '512M'); $this->setBuyItems($month, $year);
//        $this->setRebuyNos($month, $year);
//        $this->setInvoiceNos($month, $year);
//        $this->getPohodaRebuys($month, $year);
//        $this->getPohodaCommands($month, $year);
        $this->getPohodaIntern($month, $year);
//$this->repairRelation($month, $year);
//        return view('home');
//        } catch(\Exception $e) {

//        }









//        $xmlPohoda = new XmlPohoda(new \DOMDocument());
//        $xmlPohoda->initRebuys();
//
//        foreach($buyCommands as $buyCommand)
//        {
//            $xmlPohoda->addRebuy($buyCommand);

        // export objednavek
//        $commands =  $this->commandRepository->getSoldByMonth(12, 2022)->get();
//        $i = 1;
//
//        $xmlPohoda = new XmlPohoda(new \DOMDocument());
//        $xmlPohoda->initCommands();
//        foreach($commands as $command) {
//            $xmlPohoda->addCommand($command);
//            if ($i > 2) {
//                break;
//            }
//            $i++;

//            echo $command->id . "\n";
//            $command->invoice_no = "F2212" . str_pad($i, 3, "0", STR_PAD_LEFT);
//            $command->save();


//            $date = date("d", strtotime($command->status->date_paid));
//            $commandIndexes = array_filter($dates, function ($d) use ($date){return $d < $date;});
//            foreach ($command->items as $item) {
//                $buyItem = new BuyItem();
//                $buyItem->id_product = $item->stock->all_product_id;
//                $buyItem->id_stock = $item->stock->id;
//                $buyItem->id_language = $item->stock->language_id;
//                $buyItem->price = round($item->price * 0.6, 2);
//                $buyItem->quantity = $item->quantity;
//                $buyItem->state = $item->stock->state;
//                $buyItem->isFoil = $item->stock->isFoil;
//                $buyItem->playset = $item->stock->playset;
//                $buyItem->signed = $item->stock->signed;
//                $buyItem->altered = $item->stock->altered;
//                $buyItem->added = 1;
//                $buyItem->sold_quantity = $item->quantity;
//                $usedBuyCommand = $buyCommands->get(rand(0,count($commandIndexes) - 1));
//                $usedBuyCommand->items()->save($buyItem);
//            }


//        }
//echo ($xmlPohoda->getXml());


        //$this->giftItemRepository->getRandomGifts(1,4);

        /*
                $file = Storage::get('MKMResponses/stockFile.csv');
                $datas = explode("\n", $file);
                $datas = array_slice($datas, 1, count($datas) - 2);
                $i = 0;
                $mkmStock = collect();
                foreach ($datas as $data) {
                    $mkmStock->add(new StockFileItem(str_getcsv($data, ";")));
                }
                $mkmIds = $mkmStock->map(function ($item, $key) {
                    return $item->idArticle;
                })->toArray();
                /*
                $a = array();
                foreach ($datas as $data){
                    $a[$i++]=(str_getcsv($data, ";")[0]);
                }
                */
//        $test = $this->MKMService->addToStock(494824,1,10);
        //$item = BuyItem::where('id_product', 495464)->first();
        //$test = $this->stockService->addFromBuy($item);
//        \Debugbar::info($test);
        /*$dateStock = \Storage::lastModified('MKMResponses/stockFile.csv');
        $i = 1;
        $orders = array();
        do {
            $answer = $this->MKMService->getSellerOrders("received", $i);
            if ($answer == null)
                break;
            $orders = array_merge($orders, $answer->order);
            $i += 100;
        } while (1);
        $test = $orders;*/
//        $order = $this->MKMService->getOrder(1067221481);
        //$test = $this->commandRepository->getByIdMKM( $order->idOrder)->id;
        //$buyCommand = $this->buyCommandRepository->getById(2);
        //$test = $this->buyItemRepository->getByStocking($buyCommand,2);
//        \Debugbar::info($order);
       /* \Debugbar::info(
        StockChange::max("batch")
    );*/
        return view('home');
    }

    public function testPdf(pdfFacture $fpdf){
        $commands = $this->commandRepository->getCommandsByType(4);
        foreach ($commands as $command){
            $fpdf->show($command);
        }
      $fpdf->Output("I","Addresses.pdf", true);
//        return view('home');
    }
}
