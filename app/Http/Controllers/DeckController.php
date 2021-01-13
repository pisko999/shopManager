<?php

namespace App\Http\Controllers;

use App\Repositories\DeckRepository;
use App\Repositories\DeckRepositoryInterface;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    private $deckRepository;
    public function __construct(DeckRepositoryInterface $deckRepository)
    {
        $this->deckRepository = $deckRepository;
    }

    public function index(){
        $decks = $this->deckRepository->getPaginate(25);
        $links = $decks->render();
        return view('deck.index', compact('decks', 'links'));
    }

    public function show($id){
        $deck = $this->deckRepository->getById($id);
        return view('deck.show',compact('deck'));
    }

    public function create(Request $request){
        return $this->deckRepository->store($request->all());
    }

    public function check($id){

    }
}
