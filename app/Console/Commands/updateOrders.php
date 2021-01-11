<?php

namespace App\Console\Commands;

use App\Repositories\CommandRepositoryInterface;
use App\Services\MKMService;
use Illuminate\Console\Command;

class updateOrders extends Command
{
    private $commandRepository;
    private $mkmService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateOrders';

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
        $this->mkmService = $MKMService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $commands = $this->commandRepository->getByTypeFromMKM("sent", true);
        foreach ($commands as $command){
            $order = $this->mkmService->getOrder($command->idOrderMKM);
            $changed = $this->commandRepository->checkStatus($command->id, $order);

            if ($changed)
                echo "Order #" . $command->idOrderMKM . " updated.\n";
            else
                echo "Order #" . $command->idOrderMKM . " wasn`t changed.\n";
        }
    }
}
