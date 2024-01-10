<?php

namespace App\Console\Commands;

use App\Models\BuyCommand;
use App\Models\BuyItem;
use App\Models\Status;
use App\Models\User;
use App\Repositories\BuyCommandRepository;
use App\Repositories\BuyCommandRepositoryInterface;
use App\Repositories\CommandRepositoryInterface;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;

class createRebuyOrders extends Command
{

    protected $commandRepository;
    protected $buyCommandRepository;

    public function __construct(CommandRepositoryInterface $commandRepository, BuyCommandRepositoryInterface $buyCommandRepository)
    {
        parent::__construct();
        $this->commandRepository = $commandRepository;
        $this->buyCommandRepository = $buyCommandRepository;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:createRebuyOrders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create rebuy orders to sold items';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $users = User::whereIn('email', ['janpopela@mtgforfun.cz', 'filipdoskocil@mtgforfun.cz', 'filiplukesf@mtgforfun.cz', 'ondrejlikesf@mtgforfun.cz', 'pavelbednar@mtgforfun.cz']);
//        echo $users->count();
//        $i = 0;
//        foreach($users as $user) {
//            $i++;
//            echo $user->name;
//            $status = new Status();
//            $status->status_id = 10;
//            $status->save();
//            $buyCommand = new BuyCommand();
//            $buyCommand->document_no = 'R221200' . $i;
//            $buyCommand->status->attach($status);
//            $user->buyCommands->attach($buyCommand);
//            $buyCommand->save();
//        }
        /*
        $buyCommands = $this->buyCommandRepository->getBoughtByMonth(12,2022)->get();
        $commands =  $this->commandRepository->getSoldByMonth(12, 2022)->get();
        foreach($commands as $command) {
            echo $command->id . "\n";
            foreach ($command->items as $item) {
                echo $item->stock->product?->name . "\n";
                $this->table([], $item->stock->buyItems);
            }
        }
//        $this->table([], $commands);
        echo $commands->count(0);
        */
        $month = 2;
        $year = 2023;

        $dates = [3, 7, 12, 26, 28];
        $users = User::whereIn('email', ['janpopela@mtgforfun.cz', 'filipdoskocil@mtgforfun.cz', 'filiplukesf@mtgforfun.cz', 'ondrejlikesf@mtgforfun.cz', 'pavelbednar@mtgforfun.cz'])->get();
        echo $users->count();
        $i = 0;
        foreach($users as $user) {
            $date = Carbon::create($year, $month, $dates[$i]);
            $i++;
            echo $user->name;
            $status = new Status();
            $status->status_id = 10;
            $status->date_bought = $date;
            $status->date_paid = $date;
            $status->save();
            $buyCommand = new BuyCommand();
            $buyCommand->document_no = 'R221200' . $i;
            $user->buyCommands()->save($buyCommand);
            $buyCommand->save();
            $buyCommand->status()->associate($status)->save();
        }

        $buyCommands = BuyCommand::whereNotNull('document_no')->get();
        $commands =  $this->commandRepository->getSoldByMonth($month, $year)->get();
        foreach($commands as $command) {
//            echo $command->id . "\n";
//            $command->invoice_no = "F2212" . str_pad($i, 3, "0", STR_PAD_LEFT);
//            $command->save();
            $date = date("d", strtotime($command->status->date_paid));
            $commandIndexes = array_filter($dates, function ($d) use ($date){return $d < $date;});
            foreach ($command->items as $item) {
                $buyItem = new BuyItem();
                $buyItem->id_product = $item->stock->all_product_id;
                $buyItem->id_stock = $item->stock->id;
                $buyItem->id_language = $item->stock->language_id;
                $buyItem->price = round($item->price * 0.85, 2);
                $buyItem->quantity = $item->quantity;
                $buyItem->state = $item->stock->state;
                $buyItem->isFoil = $item->stock->isFoil;
                $buyItem->playset = $item->stock->playset;
                $buyItem->signed = $item->stock->signed;
                $buyItem->altered = $item->stock->altered;
                $buyItem->added = 1;
                $buyItem->sold_quantity = $item->quantity;
                $usedBuyCommand = $buyCommands->get(rand(0,count($commandIndexes) - 1));
                $usedBuyCommand->items()->save($buyItem);

            }
        }


        return 0;
    }
}
