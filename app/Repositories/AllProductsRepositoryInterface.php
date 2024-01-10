<?php


namespace App\Repositories;


use Illuminate\Http\Request;

interface AllProductsRepositoryInterface extends ModelRepositoryInterface
{
    public function add($data);

    public function search(Request $request) ;
}
