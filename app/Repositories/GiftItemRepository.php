<?php


namespace App\Repositories;


use App\Models\GiftItem;

class GiftItemRepository extends ModelRepository implements GiftItemRepositoryInterface
{
    public function add($quantity, $foil)
    {
        return GiftItem::Create(
            [
                'quantity' => $quantity,
                'quantity_rest' => $quantity,
                'foil' => $foil,
            ]
        );

    }
    public function getRandomGifts($idGiftList, $count, $exact = true): array{
        $giftItems = GiftItem::where('gift_list_id', '=', $idGiftList)->where('quantity_rest', '>', 0)->get();
        $giftItemsOverQuantity = $giftItems->where('quantity_rest', '>', 1)->all();
        foreach($giftItemsOverQuantity as $item) {
            for($i = 1; $i < $item->quantity_rest; $i++) {
                $giftItems->push($item);
            }
        }
//        $coll = $giftItems->map(function($item){
//            return $item->all_product_id;
//        })->toArray();
//        sort($coll);
//        \Debugbar::info($coll);
        if ($count > $giftItems->count()) {
            $count = $giftItems->count();
        }
        $giftItems = $giftItems->random($count)->all();

        foreach($giftItems as $item) {
            $item->quantity_rest--;
            $item->save();
        }
        return $giftItems;
    }
    public function getItemsCount($idGiftList){
        return GiftItem::where('gift_list_id', '=', $idGiftList)->where('quantity_rest', '>', 0)->count();
    }

}
