<?php

namespace App\Console\Commands;

use App\Repositories\CommandRepositoryInterface;
use App\Services\MKMService;
use Illuminate\Console\Command;

class getOldOrders extends Command
{
    private $commandRepository;
    private $MKMService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getOldOrders';

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
    public function __construct(CommandRepositoryInterface $commandRepository, MKMService $MKMService)
    {
        $this->commandRepository = $commandRepository;
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
        $dateStock = \Storage::lastModified('MKMResponses/stockFile.csv');
        $states = ['received', 'lost', 'cancelled']; // 'received', 'lost', 'cancelled'
        foreach ($states as $state) {
            $orders = $this->MKMService->getSellerOrders($state);
            if (isset($orders->link)) {
                $i = 1;
                $orders = array();
                do {
                    $answer = $this->MKMService->getSellerOrders("received", $i);
                    if ($answer == null)
                        break;
                    $orders = array_merge($orders, $answer->order);
                    $i += 100;
                } while (1);

            }
            else
                $orders = $orders->order;
            //var_dump($orders);

            foreach ($orders as $order) {
                $command = null;
                $command = $this->commandRepository->getByIdMKM($order->idOrder);
                if (!$command) {
                    $command = $this->commandRepository->createFromMKM($order, $dateStock);
                    echo "Order #" . $command->idOrderMKM . " was added.\n\t" . $command->items->count() . " items added\n";
                    continue;
                } else {
                    $changed = $this->commandRepository->checkStatus($command->id, $order);
                    if ($changed)
                        echo "Order #" . $command->idOrderMKM . " updated.\n";
                    else
                        echo "Order #" . $command->idOrderMKM . " wasn`t changed.\n";
                }

            }
        }
        return 0;
    }
}
