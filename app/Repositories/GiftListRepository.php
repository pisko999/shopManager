<?php


namespace App\Repositories;


use App\Models\GiftList;

class GiftListRepository extends ModelRepository implements GiftListRepositoryInterface
{
    public function __construct(GiftList $giftList)
    {
        $this->model = $giftList;
    }
    public function add($name)
    {
        return GiftList::Create(
            [
                'name' => $name,
                'status' => 'active',
            ]
        );

    }
}
