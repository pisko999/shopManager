<?php


namespace App\Repositories;


interface GiftRepositoryInterface extends ModelRepositoryInterface
{
    public function add($data);
    public function getByGiftList($idGiftList) ;
}

