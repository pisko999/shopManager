<?php


namespace App\Repositories;


interface BuyCommandRepositoryInterface extends ModelRepositoryInterface
{
    public function add($data);

    public function new();

    public function saveEdition($data);
    public function getBoughtByMonth($month, $year);
    public function getBoughtFromSroBeginning($month, $year);
}
