<?php


namespace App\Repositories;


interface GiftListRepositoryInterface extends ModelRepositoryInterface
{
    public function add($name);
}
