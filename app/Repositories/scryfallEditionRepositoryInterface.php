<?php


namespace App\Repositories;


interface scryfallEditionRepositoryInterface extends ModelRepositoryInterface
{
public function add($set);
public function getAllWithExpansions();
public function getByCode($code);
}
