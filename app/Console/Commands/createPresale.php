<?php

namespace App\Console\Commands;

use App\Models\BuyCommand;
use App\Models\BuyItem;
use App\Models\Expansion;
use App\Models\Status;
use App\Models\User;
use App\Objects\Conditions;
use App\Repositories\BuyCommandRepositoryInterface;
use App\Repositories\CommandRepositoryInterface;
use App\Repositories\ExpansionRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class createPresale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:createPresale {sign} {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vytvori stock pro pre-sale';


    protected $userRepository;
    protected $expansionRepository;
    protected $buyCommandRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ExpansionRepositoryInterface $expansionRepository,
        BuyCommandRepositoryInterface $buyCommandRepository
    )
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->expansionRepository = $expansionRepository;
        $this->buyCommandRepository = $buyCommandRepository;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = $this->userRepository->getById(2985);
        $date = Carbon::create();
        $sign = strtoupper($this->argument('sign'));
        $id = $this->argument('id');
        $buyCommand = null;
        $quantities = [
            'C' => 4,
            'U' => 4,
            'R' => 4,
            'M' => 2,
        ];
        if ($id) {
            $buyCommand = $this->buyCommandRepository->getById($id);
        }
        if(!$buyCommand) {
            $status = new Status();
            $status->status_id = \App\Objects\Status::REBUY;
            $status->date_bought = $date;
            $status->date_paid = $date;
            $status->save();
            $buyCommand = new BuyCommand();
            $user->buyCommands()->save($buyCommand);
            $buyCommand->save();
            $buyCommand->status()->associate($status)->save();
            echo $buyCommand->id;
        }
        $expansion = $this->expansionRepository->getBySign($sign);
        echo $expansion->idMKM . "\t" . $expansion->name . "\tCards:" . $expansion->allCards->count() . "\n";
        foreach($expansion->allCards as $card) {
            $priceGuide = $card->priceGuide()->first();
//            echo $card->id . "\t" .$card->name . "\t" . $card->stock->count() . "\n";
            if ($card->stock->count() || $card->card == null || empty($priceGuide)) {
                continue;
            }
            $price = $priceGuide->trend;
            if ($price == 0) {
                $price = $priceGuide->lov;
            }
            if ($price == 0) {
                continue;
            }
            echo $card->added . "\t" . $card->name. "\t" . $card->price . "\n";
            $buyItem = new BuyItem();
            $buyItem->id_product = $card->id;
            $buyItem->id_language = 1;
            $buyItem->price = round($price * 0.85, 2);
            $buyItem->quantity = $quantities[$card->card->rarity->sign];
            $buyItem->state = Conditions::NM;
            $buyItem->isFoil = 0;
            $buyItem->playset = 0;
            $buyItem->signed = 0;
            $buyItem->altered = 0;
            $buyItem->is_new = 1;
            $buyCommand->items()->save($buyItem);
        }
    }
}
