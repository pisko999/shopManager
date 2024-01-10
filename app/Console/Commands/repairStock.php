<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use App\Objects\StockFileItem;
use App\Repositories\StockRepository;
use App\Repositories\StockRepositoryInterface;
use App\Services\MKMService;
use Illuminate\Support\Facades\Storage;

class repairStock extends Command
{
    private $MKMService;
    private $stockRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:repairStock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(StockRepositoryInterface $stockRepository, MKMService $MKMService)
    {
	$this->MKMService = $MKMService;
        $this->stockRepository = $stockRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
//	Storage::delete('MKMResponses/stockFile.csv');
//	Storage::delete('MKMResponses/stock/file/data.json');

//	var_dump($this->MKMService->saveStockFile());

        // try if file exists
        try {
            //$file = Storage::get('MKMResponses/stockFile.csv');
            $file = Storage::get('MKMResponses/stockFilecopy.csv');
        } catch (\Exception $e) {
            echo "File not found";
            return;
        }

        //exploding stock file to lines
        $datas = explode("\n", $file);
        $datas = array_slice($datas, 1, count($datas) - 2);

        // saving time of begin
        $t1 = time();
        
        Storage::put('repairStock.log', '');
        $count = count($datas);
        $i = 0;
        foreach ($datas as $data) {
            $i++;
            $item = new StockFileItem(str_getcsv($data, ";"));
            $stock = $this->stockRepository->getByIdArticleMKM($item->idArticle);
            if ($stock) {
		if ($stock->quantity != $item->amount) {
            	    $stock->quantity = $item->amount;
            	    $stock->save();
            	    echo $i . '/' . $count . "\t" . $item->foil . " " . $item->enName . " saved\n";
		} else {
                //    echo $i . '/' . $count . "\t\t" . $item->enName . " skipped\n";
		}
            } else {
                Storage::append('repairStock.log', $item->idArticle . ';' . $item->enName . ';' . $item->price . ';' . $item->amount. ';' . "\n");
            }
        }
        
        
        
        return 0;
    }
}
