<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Repositories\CardRepositoryInterface;
use App\Repositories\ExpansionRepositoryInterface;
use App\Repositories\StockRepositoryInterface;
use App\Services\messagerieService;
use App\Services\MKMService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class stockController extends Controller
{
    private StockRepositoryInterface $stockRepository;
    public function __construct(StockRepositoryInterface $stockRepository)
    {
        $this->middleware('auth');
        $this->stockRepository = $stockRepository;
    }

    public function getMKMStock(StockRepositoryInterface $stockRepository, MKMService $mkm)
    {
        $i = 1;
        //do{
        $stock = $mkm->getStock($i);
        foreach ($stock->article as $item) {
            $stockRepository->addFromMKM($item);
        }
        $i += 100;
        \Debugbar::info($stock);

        //}while(count($stock->article)> 0);
        \Debugbar::info($stock);
        return view('getMKMStock');
    }

    public function getMKMStockFile(MKMService $MKMService)
    {
        $MKMService->saveStockFile();
        return redirect()->back();
    }

    public function setStockFromFile(StockRepositoryInterface $stockRepository, MKMService $MKMService)
    {
        $MKMService->saveStockFile();
        try {
            $file = Storage::get('MKMResponses/stockFile.csv');
        } catch (\Exception $e) {
            messagerieService::errorMessage("File with stock does not exists.");
            return redirect()->back();
        }
        $datas = explode("\n", $file);
        $datas = array_slice($datas, 1);

        foreach ($datas as $data)
            if ($data != '')
                $stockRepository->addFromCSV($data);

        \Debugbar::info($data);
        return view('home');
    }

    public function stockingShowGet(ExpansionRepositoryInterface $expansionRepository)
    {
        $editions = $expansionRepository->getArrayForSelect();
        $r = 'stockingShowPost';
        return view('editionSelectGet', compact('editions', 'r'));
    }

    public function stockingShowPost(Request $request, CardRepositoryInterface $cardRepository)
    {

        $cards = $cardRepository->getCardsByEditionWithProductAndColorsWithoutFoil($request->edition);
        $maxCard = $cardRepository->getCardByNameAndEdition("Plains", $request->edition)->first();
        if ($maxCard != null)
            $cards = $cards->filter(function ($value) use ($maxCard) {
                return $value->scryfallCollectorNumber < $maxCard->scryfallCollectorNumber;
            });
        $colors = ['White', 'Blue', 'Black', 'Red', 'Green'];
        $max = 0;
        foreach ($colors as $color) {
            //$list[$color] = $cardRepository->getCardsByEditionAndColor($request->edition, $color)->values();
            $list[$color] = $cards->filter(function ($value) use ($color) {
                return count($value->colors) == 1 && $value->colors[0]->name == $color;
            })->values();
            if (count($list[$color]) > $max)
                $max = count($list[$color]);
        }

        $list["Multicolor"] = $cards->filter(function ($value) {
            return count($value->colors) > 1;
        })->values();

        $list["Colorless"] = $cards->filter(function ($value) {
            return count($value->colors) < 1;
        })->values();
        \Debugbar::info($list);

        return view('showStocking', compact('list', 'colors', 'max'));
    }

    public function stockEditSelect(ExpansionRepositoryInterface $expansionRepository)
    {

        $editions = $expansionRepository->getArrayForSelect();
        $r = 'stockEditGet';
        $m = 'get';
        $requireFoilSelect = true;
        return view('editionSelectGet', compact('editions', 'r', 'm', 'requireFoilSelect'));
    }

    public function stockEditGet(Request $request, ExpansionRepositoryInterface $expansionRepository)
    {
        $expansion = $expansionRepository->getByMKMId($request->id);

        $stock = $expansion->getStockWithRelationsPaginate($request->foils);
        $links = $stock->render();
//        \Debugbar::info($stock);
        return view('stock.expansion', compact('expansion', 'stock', 'links'));
    }
public function stockUpdateQuantity($id,Request $request, StockRepositoryInterface $stockRepository, StockService $stockService){
        $stock = $stockRepository->getById($id);
        if(!$stock)
            return 404;
        switch($request->action){
            case "decrease":
                $result = $stockService->decrease($stock,$request->quantity);
                break;
            case "increase":
                $result = $stockService->increase($stock,$request->quantity);
                break;
            default:
                $result = null;
        }
        return $result;
    }

    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Auth\Access\Response|bool|\Illuminate\Contracts\Foundation\Application
    {
        $stock = $this->stockRepository->getStock($request);
        $links = $stock->render();
        return view('stock.index', compact('stock', 'links'));
    }
    public function edit(int $id = null) {
        if ($id) {
            $stock = $this->stockRepository->getById($id);
        } else {
            $stock = new Stock();
        }
        return view('stock.edit', compact('stock'));
    }
}
