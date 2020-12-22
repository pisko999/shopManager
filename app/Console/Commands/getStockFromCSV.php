<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Stock;
use App\Repositories\StockRepositoryInterface;
use App\services\messagerieService;
use App\Services\MKMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class getStockFromCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getStockFromCSV';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $stockRepository;
    private $MKMService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(StockRepositoryInterface $stockRepository, MKMService $MKMService)
    {
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
        $this->MKMService->saveStockFile();
        try {
            $file = Storage::get('MKMResponses/stockFile.csv');
        } catch (\Exception $e) {
            echo "File not found";
            return;
        }
        $datas = explode("\n", $file);
        $datas = array_slice($datas, 1);

        $i = 0;
        $count = count($datas);

        /* test
            $ids = array();
            $time = time();
        */
        foreach ($datas as $data) {
            if ($data != '') {

                $data = str_replace('"', '', $data);
                $item = explode(";", $data);

                $this->stockRepository->addFromCSV($item);
                /*test
                {

                    $data = str_replace('"', '', $data);
                    $item = explode(";", $data);
                    array_push($ids, $item[0]);

                }
                */
            }
            $i++;
            echo $i . '/' . $count . "\n";
        }
        /*test
        $stockIds = Stock::pluck('idArticleMKM')->toArray();
        var_dump(count($stockIds));

        $stock = Stock::whereNotIn('idArticleMKM', $ids)->get();
        var_dump(count($stock));
        echo time() - $time;
        var_dump(count($ids));
        var_dump(
            count(
                array_diff(
                    $ids,
                    $stockIds)));
        */

        $categories = DB::table('stocks')->
        Join('all_products', 'stocks.id', '=', 'all_products.id')->
        select('idCategory')->
        distinct()->
        get();

        foreach ($categories as $category) {
            $cat = Category::find($category->idCategory);
            if ($cat != null) {
                $cat->haveArticles = true;
                $cat->save();
            }
        }

    }
}
