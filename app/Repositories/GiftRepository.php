<?php


namespace App\Repositories;


use App\Models\Gift;
use Illuminate\Database\Eloquent\Builder;

class GiftRepository extends ModelRepository implements GiftRepositoryInterface
{
    public function __construct(Gift $gift)
    {
        $this->model = $gift;
    }
    public function add($idCommand)
    {
        $category = Gift::Create(
            [
                'id_command' => $idCommand,
                'status' => 'active',
            ]
        );

    }

    public function getByGiftList($idGiftList) {
        return $this->model->whereHas('giftItems', function (Builder $query) use ($idGiftList) {
            $query->where('gift_list_id', '=', $idGiftList);
        })->with('giftItems')->with('giftItems.product')
            ->with('giftItems.product.card')
            ->with('giftItems.product.expansion')
            ->with('giftItems.product.card.rarity')
            ->with('giftItems.product.priceGuide')
            ->get();
    }
}
