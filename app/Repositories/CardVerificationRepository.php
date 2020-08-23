<?php


namespace App\Repositories;


use App\Models\CardVerification;
use App\Models\Expansion;
use App\Models\ScryfallEdition;

class CardVerificationRepository extends ModelRepository implements CardVerificationRepositoryInterface
{
    public function __construct(CardVerification $cardVerification)
    {
        $this->model = $cardVerification;
    }

    public function add($cardEx, Expansion $expansion, ScryfallEdition $edition, $scryfallCard = null)
    {
        $exists = $this->model->where('all_product_id', $cardEx->id)->get();
        if ($exists->count() == 0) {
            $this->model = new CardVerification();
            $this->model->Card()->associate($cardEx);
            $this->model->Expansion()->associate($expansion);
            $this->model->ScryfallEdition()->associate($edition);
            if ($scryfallCard != null)
                $this->model->scryfall_card_id = $scryfallCard;
            $this->model->save();
        }
    }
    public function getByProductId($id){
        return $this->model->where('all_product_id', $id)->first();
    }

}
