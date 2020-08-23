<?php


namespace App\Repositories;


use App\Models\Expansion;
use App\Models\ScryfallEdition;

interface CardVerificationRepositoryInterface extends ModelRepositoryInterface
{
    public function add($cardEx, Expansion $expansion, ScryfallEdition $edition,$scryfallCard = null);
    public function getByProductId($id);
}
