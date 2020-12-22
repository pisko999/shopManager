<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Objects\StockFileItem;
use App\Repositories\StockRepository;
use App\Repositories\StockRepositoryInterface;
use App\Services\MKMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class checkStock extends Command
{
    private $MKMService;
    private $changedNames;
    private $stockRepository;
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
    public function __construct(StockRepositoryInterface $stockRepository, MKMService $MKMService)
    {
        $this->stockRepository = $stockRepository;
        $this->MKMService = $MKMService;
        $this->changedNames = array();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Storage::delete('MKMResponses/stockFile.csv');
        Storage::delete('MKMResponses/stock/file/data.json');
        $this->MKMService->saveStockFile();
        try {
            $file = Storage::get('MKMResponses/stockFile.csv');
        } catch (\Exception $e) {
            echo "File not found";
            return;
        }
        $datas = explode("\n", $file);
        $datas = array_slice($datas, 1, count($datas) - 2);


        $ids = array();
        $t1 = time();
        $stock = $this->stockRepository->getInStock();
        //$allStock = Stock::get();


        $mkmStock = collect();
        foreach ($datas as $data) {
            $mkmStock->add(new StockFileItem(str_getcsv($data, ";")));
        }

        $mkmStockFSAP = $mkmStock->filter(function ($value, $key) {
            return $value->foil || $value->signed || $value->playset || $value->altered;
        });

        $mkmStock = $mkmStock->diffKeys($mkmStockFSAP);

        $stockFSAP = $stock->filter(function ($value, $key) {
            return $value->isFoil || $value->signed || $value->playset || $value->altered;
        });

        $stock = $stock->diffKeys($stockFSAP);

        //$answers = collect();
        /*
        $mkmStock = $mkmStock->mapToGroups(function ($item, $value) {
            return [$item->expCode => $item];
        });
        var_dump($mkmStock->first()->count());
        foreach ($mkmStock as $expansion) {
            $answers = $answers->merge($this->checkCollections())
        }
        */
                $answers = $this->checkCollections($stock, $mkmStock);
                $t2 = time();
                $answers = $answers->merge($this->checkCollections($stockFSAP, $mkmStockFSAP));

                $grouped = $answers->mapToGroups(function ($item, $key) {
                    return [$item->first()['type'] => $item->first()[0]];
                });
                $mkm = $mkmStock->merge($mkmStockFSAP);
                //$mkm = $mkmStockFSAP;
                foreach ($mkm as $item) {
                    $response = $this->stockRepository->addFromCSV2($item);
                    var_dump($response);
                }

                echo "first compare in " . ($t2 - $t1) . "s and second in " . (time() - $t2) . "s.\n";

    }

    private function checkCollections($items, &$mkmItems)
    {
        $answers = collect();
        $i = 0;
        $count = count($items);

        foreach ($items as $item) {
            echo 'stock ' . ++$i . '/' . $count . "\n";
            $mkmItem = $mkmItems->where('idArticle', '=', $item->idArticleMKM);
            if ($mkmItem->count() == 1) {
                $key = $mkmItem->keys()->first();
                $mkmItem = $mkmItem->first();
                $answer = $this->stockRepository->differentUpdate($item, $mkmItem);
                if ($answer)
                    $answers->push($answer);
                $mkmItems->forget($key);


            } elseif ($mkmItem->count() == 0) {
                $item->quantity = 0;
                $item->save();
                $answers->push(collect()->push(['type' => 'articleNoMoreOnMKM', $item->id]));
            } else {
                $answers->push(collect()->push(['type' => 'duplicateOnMKM', $item->id]));
            }
        }
        return $answers;
    }
}
