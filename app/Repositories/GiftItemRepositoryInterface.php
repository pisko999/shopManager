<?php


namespace App\Repositories;


interface GiftItemRepositoryInterface extends ModelRepositoryInterface
{
    public function add($quantity, $foil);
    public function getRandomGifts($idGiftList, $count, $exact = true);
    public function getItemsCount($idGiftList);
}
