<?php


namespace App\Repositories;


use App\Models\AllProduct;

interface CardRepositoryInterface extends ModelRepositoryInterface
{
    public function add($product, $data);
    public function exists(AllProduct $product);
}
