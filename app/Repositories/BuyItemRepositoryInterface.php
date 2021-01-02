<?php


namespace App\Repositories;


use App\Models\BuyCommand;

interface BuyItemRepositoryInterface extends ModelRepositoryInterface
{
    public function add(BuyCommand $buyCommand,$data);
    public function remove($id);
    public function increase($id, $data);
    public function decrease($id, $data);
    public function getByStocking(BuyCommand $buyCommand, $stocking);
    }
