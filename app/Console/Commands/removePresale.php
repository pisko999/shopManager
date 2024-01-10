<?php

namespace App\Console\Commands;

use App\Repositories\ExpansionRepositoryInterface;
use App\Services\StockService;
use Illuminate\Console\Command;

class removePresale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:removePresale {sign}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Odebere neprodane presale produkty';

    protected $expansionRepository;
    protected $stockService;

    public function __construct(
        ExpansionRepositoryInterface $expansionRepository,
        StockService $stockService
    )
    {
        parent::__construct();
        $this->expansionRepository = $expansionRepository;
        $this->stockService = $stockService;

    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sign = strtoupper($this->argument('sign'));
        $expansion = $this->expansionRepository->getBySign($sign);
        echo $expansion->idMKM . "\t" . $expansion->name . "\tCards:" . $expansion->allCards->count() . "\n";
        foreach($expansion->allCards as $card) {
            foreach($card->stock as $stock) {
                if ($stock->items->count() > 0 ) {
                    echo $stock->quantity . "\t" . $stock->buyItems->count() . "\t" . $card->name . "\n";
                }

                if ($stock->quantity) {
                    $quantity = $stock->quantity;
                    foreach($stock->buyItems as $buyItem) {
                        if ($quantity > $buyItem->quantity) {
                            $quantity -= $buyItem->quantity;
                            $buyItem->delete();
                        } elseif ($quantity == $buyItem->quantity){
                            $quantity = 0;
                            $buyItem->delete();
                        } else {
                            $quantity -= $stock->quantity;
                            $buyItem->save();
                            $stock->quantity = 0;
                        }
                        if($stock->quantity == 0) {
                            break;
                        }
                    }
                    $this->stockService->decrease($stock,$stock->quantity);
                    if($stock->quantity == 0 && $stock->items->count() == 0 && $stock->buyItems->count() == 0) {
                        $stock->delete();
                    }
                }
            }
        }
        return 0;
    }
}
