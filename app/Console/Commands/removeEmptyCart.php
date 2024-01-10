<?php

namespace App\Console\Commands;

use App\Models\Command;
use Carbon\Carbon;

class removeEmptyCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:removeEmptyCart';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $twoDaysAgo = Carbon::now()->subDays(2);

        $cartCommands = Command::whereHas('status.status', function($query) {
            $query->where('name', 'cart');
        })->whereDoesntHave('items', function ($query) use ($twoDaysAgo) {
            $query->where('created_at', '<', $twoDaysAgo);
        })->get();
        echo $cartCommands->count();
        return 0;
    }
}
