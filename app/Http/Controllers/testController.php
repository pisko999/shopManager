<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\BuyItem;
use App\Models\StockChange;
use App\Objects\FPDFO;
use App\Objects\pdfAddress;
use App\Objects\pdfFacture;
use App\Objects\StockFileItem;
use App\Repositories\BuyCommandRepositoryInterface;
use App\Repositories\BuyItemRepositoryInterface;
use App\Repositories\CommandRepositoryInterface;
use App\Repositories\ExpansionRepositoryInterface;
use App\Repositories\StatusNamesRepositoryInterface;
use App\Repositories\StatusRepositoryInterface;
use App\Repositories\StockRepositoryInterface;
use App\Services\MKMService;
use App\Services\StockService;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class testController extends Controller
{
    private $stockRepository;
    private $statusRepository;
    private $MKMService;
    private $stockService;
    private $buyCommandRepository;
    private $buyItemRepository;
    private $commandRepository;

    public function __construct(
        StockRepositoryInterface $stockRepository,
        StatusRepositoryInterface $statusRepository,
        MKMService $MKMService,
        StockService $stockService,
    BuyCommandRepositoryInterface $buyCommandRepository,
    BuyItemRepositoryInterface $buyItemRepository,
    CommandRepositoryInterface $commandRepository
    )
    {
        $this->stockRepository = $stockRepository;
        $this->statusRepository = $statusRepository;
        $this->MKMService = $MKMService;
        $this->stockService = $stockService;
        $this->buyCommandRepository = $buyCommandRepository;
        $this->buyItemRepository = $buyItemRepository;
        $this->commandRepository = $commandRepository;
    }

    public function test()
    {
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
        //$test = $this->MKMService->addToStock(494824,1,10);
        //$item = BuyItem::where('id_product', 495464)->first();
        //$test = $this->stockService->addFromBuy($item);
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
        $order = $this->MKMService->getProduct(536402);
        //$test = $this->commandRepository->getByIdMKM( $order->idOrder)->id;
        //$buyCommand = $this->buyCommandRepository->getById(2);
        //$test = $this->buyItemRepository->getByStocking($buyCommand,2);
        \Debugbar::info($order);
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
