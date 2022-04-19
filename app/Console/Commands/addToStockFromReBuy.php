<?php

namespace App\Console\Commands;

use App\Repositories\BuyCommandRepositoryInterface;
use App\Repositories\StatusRepositoryInterface;
use App\Services\StockService;
use Illuminate\Console\Command;

class addToStockFromReBuy extends Command
{

    private $stockService;
    private $buyCommandRepository;
    private $statusRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addToStockFromReBuy {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'addToStockFromReBuy';

    /**
     * Create a new command instance.
     *
     * @param StockService $stockService
     * @param BuyCommandRepositoryInterface $buyCommandRepository
     * @param StatusRepositoryInterface $statusRepository
     */
    public function __construct(StockService $stockService, BuyCommandRepositoryInterface $buyCommandRepository, StatusRepositoryInterface $statusRepository)
    {
        parent::__construct();
        $this->stockService = $stockService;
        $this->buyCommandRepository = $buyCommandRepository;
        $this->statusRepository = $statusRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = strtoupper($this->argument('id'));
        if(is_numeric($id)) {
            $buyCommands = array($this->buyCommandRepository->getById($id));
            $countCommands = 1;
        } else {
            $buyCommands = $this->buyCommandRepository->getClosed();
            $countCommands = $buyCommands->count();
        }
        $i = 0;
        foreach ($buyCommands as $buyCommand) {
            $i++;
            $errors = array();
            $messages = array();
            $j = 0;
            $total = 0;
            $countItems = $buyCommand->items->count();
            foreach ($buyCommand->items as $item) {
                $j++;
                echo 'Command ' . $i . '/' . $countCommands . ")  item (" . $j . '/' . $countItems . ")\n";

                if ($item->added)
                    continue;

                $stock = $this->stockService->addFromBuy($item);

                if (isset($stock->error))
                    array_push($errors, $stock->error);
                if ($stock->quantity > 20)
                    array_push($messages, $stock);
                $total += $stock->price * $item->quantity;
            }
            foreach($messages as $message) {
                var_dump($message);
            }
            foreach ($errors as $error) {
                var_dump($error);
            }
            $this->buyCommandRepository->setValue($buyCommand, $total);
            $this->statusRepository->updateStatus($buyCommand->status, "confirmed");
        }
    }
}
