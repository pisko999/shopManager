<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Complaint;
use App\Models\Evaluation;
use App\Models\Item;
use App\Models\Method;
use App\Models\ShippingMethod;
use App\Models\Status;
use App\Models\StatusName;
use App\Models\Stock;
use App\Models\User;
use App\Repositories\CommandRepository;
use App\Repositories\CommandRepositoryInterface;
use App\Services\MKMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;


class getOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getOrders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'getting orders from MKM';

    private $MKMService;
    private $commandRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MKMService $MKMService, CommandRepositoryInterface $commandRepository)
    {
        $this->MKMService = $MKMService;
        $this->commandRepository = $commandRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $dateStock = \Storage::lastModified('MKMResponses/stockFile.csv');
            $states = ['bought', 'paid', 'sent',]; // 'received', 'lost', 'cancelled'
            $orders = collect();
            foreach ($states as $state)
                $orders = $orders->merge($this->commandRepository->getByType($state, true));

            foreach ($states as $state) {
                $mkmOrders = $this->MKMService->getSellerOrders($state);
                if (isset($mkmOrders->order))


                    foreach ($mkmOrders->order as $order) {
                        $command = $this->commandRepository->getByIdMKM($order->idOrder);

                        if (!$command) {
                            $command = $this->commandRepository->createFromMKM($order, $dateStock);
                            echo "Order #" . $command->idOrderMKM . " was added.\n\t" . $command->items->count() . " items added\n";
                        } else {
                            $changed = $this->commandRepository->checkStatus($command->id, $order);
                            if ($changed)
                                echo "Order #" . $command->idOrderMKM . " updated.\n";
                            else
                                echo "Order #" . $command->idOrderMKM . " wasn`t changed.\n";
                        }
                        $key = $orders->search(function ($item, $key) use ($order) {
                            return $item->idOrderMKM == $order->idOrder ? $key : null;
                        });
                        if ($key != null)
                            $orders->forget($key);
                    }


            }

            foreach ($orders as $order) {
                $mkmOrder = $this->MKMService->getOrder($order->idOrderMKM);
                if (isset($mkmOrder->order)) {
                    $changed = $this->commandRepository->checkStatus($order->id, $mkmOrder->order);
                    if ($changed)
                        echo "Order #" . $order->idOrderMKM . " updated.\n";
                    else
                        echo "Order #" . $order->idOrderMKM . " wasn`t changed.\n";
                }
            }
        } catch (\Exception $e) {

            \Debugbar::info($e);
            \DB::rollBack();
            return null;
        }
        \DB::commit();

        return 0;
    }
}
