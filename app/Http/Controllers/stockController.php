<?php

namespace App\Http\Controllers;

use App\Repositories\CardRepositoryInterface;
use App\Repositories\ExpansionRepositoryInterface;
use App\Repositories\StockRepositoryInterface;
use App\Services\messagerieService;
use App\Services\MKMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class stockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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

    public function getMKMStockFile(MKMService $MKMService){
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

    public function stockingShowGet(ExpansionRepositoryInterface $expansionRepository){
        $editions = $expansionRepository->getArrayForSelect();
        $r = 'stockingShowPost';
        return view('editionSelectGet', compact('editions', 'r'));
    }

    public function stockingShowPost(Request $request, CardRepositoryInterface $cardRepository){

        $cards = $cardRepository->getCardsByEditionWithProductAndColorsWithoutFoil($request->edition);
        $maxCard = $cardRepository->getCardByNameAndEdition("Plains", $request->edition)->first();
        if($maxCard != null)
            $cards = $cards->filter(function($value)use ($maxCard){
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
}
