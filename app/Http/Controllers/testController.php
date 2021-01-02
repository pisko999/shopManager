<?php

namespace App\Http\Controllers;

use App\Models\BuyItem;
use App\Objects\StockFileItem;
use App\Repositories\BuyCommandRepositoryInterface;
use App\Repositories\BuyItemRepositoryInterface;
use App\Repositories\ExpansionRepositoryInterface;
use App\Repositories\StatusNamesRepositoryInterface;
use App\Repositories\StatusRepositoryInterface;
use App\Repositories\StockRepositoryInterface;
use App\Services\MKMService;
use App\Services\StockService;
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

    public function __construct(
        StockRepositoryInterface $stockRepository,
        StatusRepositoryInterface $statusRepository,
        MKMService $MKMService,
        StockService $stockService,
    BuyCommandRepositoryInterface $buyCommandRepository,
    BuyItemRepositoryInterface $buyItemRepository
    )
    {
        $this->stockRepository = $stockRepository;
        $this->statusRepository = $statusRepository;
        $this->MKMService = $MKMService;
        $this->stockService = $stockService;
        $this->buyCommandRepository = $buyCommandRepository;
        $this->buyItemRepository = $buyItemRepository;
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
        //$test = $this->MKMService->getProduct();
        $buyCommand = $this->buyCommandRepository->getById(2);
        $test = $this->buyItemRepository->getByStocking($buyCommand,2);
        \Debugbar::info($test);
        return view('home');
    }
}
