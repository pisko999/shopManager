<?php

namespace App\Console\Commands;

use App\Services\MKMService;
use Illuminate\Console\Command;

class testcommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:testcommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private $MKMService;

    public function __construct(MKMService $MKMService)
    {
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
        var_dump($this->MKMService->saveStockFile());
        echo "finished";
        return 0;
    }
}
