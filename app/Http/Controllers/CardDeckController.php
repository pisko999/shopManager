<?php

namespace App\Http\Controllers;

use App\Repositories\CardDeckRepository;
use App\Repositories\CardDeckRepositoryInterface;
use App\Repositories\DeckRepository;
use App\Repositories\DeckRepositoryInterface;
use Illuminate\Http\Request;

class CardDeckController extends Controller
{
    private $cardDeckRepository;
    private $deckRepository;

    public function __construct(CardDeckRepositoryInterface $cardDeckRepository, DeckRepositoryInterface $deckRepository)
    {
        $this->cardDeckRepository = $cardDeckRepository;
        $this->deckRepository = $deckRepository;
    }

    public function add(Request $request)
    {
        $deck = $this->deckRepository->getById($request->deckId);
        $card = $this->cardDeckRepository->getModel($request->all());
        $deck->Card()->save($card);
        return $card;
    }

    public function increase($id, Request $request)
    {
        return $this->cardDeckRepository->increase($id, $request->quantity);
    }


    public function decrease($id, Request $request)
    {
        return $this->cardDeckRepository->decrease($id, $request->quantity);
    }

    public function remove($id)
    {
        return $this->cardDeckRepository->remove($id);
    }
}
