<?php


namespace App\Repositories;


interface ExpansionRepositoryInterface extends ModelRepositoryInterface
{
    public function add($expansion);

    public function getAllWithScryfallEditions();

    public function getByIdWithScryfallEditions($id);

    public function getNonLinked();
    public function getLinked();
}
