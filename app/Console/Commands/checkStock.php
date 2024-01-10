<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Objects\StockFileItem;
use App\Repositories\StockChangesRepositoryInterface;
use App\Repositories\StockRepository;
use App\Repositories\StockRepositoryInterface;
use App\Services\MKMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class checkStock extends Command
{
    private $MKMService;
    private $stockRepository;
    private $stockChangeRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:checkStock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare stock';

    /**
     * Create a new command instance.
     *
     * @param StockRepositoryInterface $stockService
     * @param MKMService $MKMService
     */
    public function __construct(StockRepositoryInterface $stockRepository, MKMService $MKMService, StockChangesRepositoryInterface $stockChangeRepository)
    {
        $this->stockChangeRepository = $stockChangeRepository;
        $this->stockRepository = $stockRepository;
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
        //delete old stockfiles
        Storage::delete('MKMResponses/stockFile.csv');
        Storage::delete('MKMResponses/stock/file/data.json');

        // //getting actual stock
	$response = $this->MKMService->saveStockFile();


        // try if file exists
        try {
            $file = Storage::get('MKMResponses/stockFile.csv');
        } catch (\Exception $e) {
            echo "File not found";
            return;
        }

        //exploding stock file to lines
        $datas = explode("\n", $file);
        $datas = array_slice($datas, 1, count($datas) - 2);

        // saving time of begin
        $t1 = time();

        //getting whole stock on server
        //return all where quantity > 0
        $stock = $this->stockRepository->getInStock();
        //making collection of StockFileItem from StockFile
        $mkmStock = collect();
        foreach ($datas as $data) {
            $mkmStock->add(new StockFileItem(str_getcsv($data, ";")));
        }

        //filtering collection of foils, signed, playset and altered cards to make collection smaller due to time needed
         // todo: languages
        $mkmStockFSAP = $mkmStock->filter(function ($value, $key) {
            return $value->foil || $value->signed || $value->playset || $value->altered || $value->language != 1;
        });

        //filtering rest of cards
        $mkmStock = $mkmStock->diffKeys($mkmStockFSAP);

        //filtering foil, signed , playset and altered cards from server
        $stockFSAP = $stock->filter(function ($value, $key) {
            return $value->isFoil || $value->signed || $value->playset || $value->altered || $value->language_id != 1;
        });

        //filtering rest of cards from server
        $stock = $stock->diffKeys($stockFSAP);

        echo "MKM:" . count($mkmStock) . "\n";
        echo "MKMfoil:" . count($mkmStockFSAP) . "\n";
        echo "Store:" . count($stock) . "\n";
        echo "StoreFoil:" . count($stockFSAP) . "\n";
        

        //checking collection of normal cards
        // answer contain collection of changes with ids of changed articles
        // $answers = collect();
        $answers = $this->checkCollections($stock, $mkmStock);

        //saving time after first and bigger check
        $t2 = time();

        //checking second collection of foil, signed, playset and altered cards
        // answer contain collection of changes with ids of changed articles
        $answers = $answers->merge($this->checkCollections($stockFSAP, $mkmStockFSAP));

        // grouping by type of change
        $grouped = $answers->mapToGroups(function ($item, $key) {
            return [$item->first()['type'] => $item->first()[0]];
        });

        // merging collections of items missing on server
        echo "MKM:" . count($mkmStock) . "\n";
        echo "MKMfoil:" . count($mkmStockFSAP) . "\n";

        $mkm = $mkmStock->merge($mkmStockFSAP);

        // adding all items missing on server
        $first = true;
        foreach ($mkm as $item) {
            $response = $this->stockRepository->addFromCSV2($item);
            if(!$response->wasRecentlyCreated && $item->amount != $response->quantity) {
                $response->quantity = $item->amount;
                $response->save();
            }
            if($first) {
                var_dump($response);
            $first = false;
            }
        }
//        var_dump($grouped);
        foreach ($grouped as $key => $values) {
            foreach ($values as $value){}
            //    $this->stockChangeRepository->add($key, $value);
        }
        echo "first compare in " . ($t2 - $t1) . "s and second in " . (time() - $t2) . "s.\n";

    }

    private function checkCollections($items, &$mkmItems)
    {
        // collection for answers
        $answers = collect();

        //counters for output
        $i = 0;
        $count = count($items);

        // looping over item on server
        foreach ($items as $item) {

            echo 'stock ' . ++$i . '/' . $count . "\n";

            //getting item from mkm by its mkm id
            $mkmItem = $mkmItems->where('idArticle', '=', $item->idArticleMKM);

            // checking if exactly 1 exists in collection
            if ($mkmItem->count() == 1) {

                //getting its key and taking it out from collection
                $key = $mkmItem->keys()->first();
                $mkmItem = $mkmItem->first();

                //checking if is different getting collection of changes
                $answer = $this->stockRepository->differentUpdate($item, $mkmItem);

                //if some changes made, collect them
                if ($answer != null)
                    $answers->push($answer);
                $mkmItems->forget($key);

                //else if article no more exists on MKM ( idmkm doesnt match to any id from MKM)
            } elseif ($mkmItem->count() == 0) {
                if ($item->product == null)
                    var_dump($item);
                else
                    //if is card set quantity to 0 and save
                    if ($item->product->idCategory == 1) {
                        $item->quantity = 0;
                        $item->save();

                        //collect that it happens
                        $answers->push(collect()->push(['type' => 'articleNoMoreOnMKM', [$item->id, $item->idArticleMKM]]));
                    }
            } else {

                // duplicate --- should never happend
                //foreach ($mkmItem as $item){

                //}
                $answers->push(collect()->push(['type' => 'duplicateOnMKM', [$item->id, $mkmItem->idArticle]]));
            }
        }

        // return collection of all changes
        return $answers;
    }
}
