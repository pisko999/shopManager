<?php


namespace App\Repositories;


interface ExpansionRepositoryInterface extends ModelRepositoryInterface
{
    public function add($expansion);

    public function getAllWithScryfallEditions();

    public function getByIdWithScryfallEditions($id);

    public function getNonLinked();
    public function getLinked();
    public function getByMKMId($id);
    public function getAllByReleased();

    public function getAllGrouped($column);
    public function getByIds($Ids);
    public function getBySign($sign);
}
