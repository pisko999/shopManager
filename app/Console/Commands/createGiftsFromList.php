<?php

namespace App\Console\Commands;

use App\Models\Gift;
use App\Repositories\GiftItemRepository;
use App\Repositories\GiftItemRepositoryInterface;
use App\Repositories\GiftListRepository;
use App\Repositories\GiftListRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class createGiftsFromList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CreateGiftsFromList  {id} {count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates gifts from giftList';

    protected GiftItemRepositoryInterface $giftItemRepository;
    protected GiftListRepositoryInterface $giftListRepository;
    public function __construct(
        GiftItemRepositoryInterface $giftItemRepository,
        GiftListRepositoryInterface $giftListRepository
    )
    {
        parent::__construct();
        $this->giftItemRepository = $giftItemRepository;
        $this->giftListRepository = $giftListRepository;

    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (empty($this->argument('id')) || empty($this->argument('count'))) {
            return;
        }
        $giftList = $this->giftListRepository->getById($this->argument('id'));
        if (!$giftList) {
            return;
        }
        DB::beginTransaction();
        try {
            $i = 0;
            while($giftList->freeGiftItems->count()) {
                $i++;
                error_log($i . '-' . $giftList->freeGiftItems->count());
                $gift = new Gift();
                $gift->save();

                $giftItems = $this->giftItemRepository->getRandomGifts($this->argument('id'), $this->argument('count'));
                foreach ($giftItems as $giftItem) {
                    $giftItem->gifts()->attach($gift, ['quantity' => 1]);
                }
                $giftList->load('freeGiftItems');
            }

        } catch (\Exception $e) {
            // An error occurred; cancel the transaction...
            DB::rollBack();

            // and rethrow the exception
            var_dump($e);
            return false;
        }
        DB::commit();

        return 0;
    }
}
