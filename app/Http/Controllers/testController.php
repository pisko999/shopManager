<?php

namespace App\Http\Controllers;

use App\Objects\StockFileItem;
use App\Repositories\ExpansionRepositoryInterface;
use App\Repositories\StockRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class testController extends Controller
{
    public  function test(StockRepositoryInterface $stockRepository){

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
        $test = $stockRepository->getInStockInArray($mkmIds)->get();
        //\Debugbar::info($test->get());
        return view('home');
    }
}
