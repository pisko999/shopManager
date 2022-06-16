<?php

namespace App\Console\Commands;

use App\Repositories\BuyCommandRepositoryInterface;
use App\Services\StockService;
use Illuminate\Console\Command;

class deleteByBuyCommand extends Command
{

    private $stockService;
    private $buyCommandRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:deleteByBuyCommand {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete complete stock of one expansion';


    public function __construct(StockService $stockService, BuyCommandRepositoryInterface $buyCommandRepository)
    {
        parent::__construct();
        $this->stockService = $stockService;
        $this->buyCommandRepository = $buyCommandRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $id = strtoupper($this->argument('id'));
        if(!is_numeric($id)) {
            return 0;
        }
        $buyCommand = array($this->buyCommandRepository->getById($id))[0];
        $i = 0;
        $items = [];
        foreach ($buyCommand->items as $item) {
            echo $i . "\n";
            $i++;
            if(!$item->stock) {
                continue;
            }
            $items[] = $item->stock;
            $item->added = 0;
            $item->save();
            if ($i >= 100) {
                $this->stockService->deleteMany($items);
                $i = 0;
                $items = [];
            }
        }
        $this->stockService->deleteMany($items);
    }
}
