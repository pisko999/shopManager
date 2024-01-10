<?php

namespace App\Http\Controllers;

use App\Repositories\ExpansionRepository;
use App\Repositories\ExpansionRepositoryInterface;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ExpansionController extends Controller
{
    private ExpansionRepository $expansionRepository;

    public function __construct(ExpansionRepositoryInterface $expansionRepository) {
        $this->expansionRepository = $expansionRepository;
        $this->middleware('auth');
    }
    public function show(): bool|Response|Application|Factory|View {
        $expansions = $this->expansionRepository->getAllGrouped('type');
        return view('showExpansionsCheckbox', compact('expansions'));
    }
    public function showCards(Request $request): bool|Response|Application|Factory|View {
        \Debugbar::info($request->ids);
        $expansions = $this->expansionRepository->getByIds($request->ids);

        return view('showExpansionCards', compact('expansions'));
    }

    public function index($idGame = null, $type = null): bool|Response|Application|Factory|View {
        $expansions = collect();
        $games = collect([1 => "Magic: the Gathering", 6 => "Pokemon", 20 => "Battle Spirits Saga"]);
        if ($idGame) {
            $expansions = $this->expansionRepository->getAllGrouped('type', $idGame);
        }
        return view('expansion.index', compact('expansions', 'games', 'idGame', 'type'));
    }

    public function showExpansion($id): bool|Response|Application|Factory|View {
        $expansion = $this->expansionRepository->getById($id);
        $soldPrice = 0;
        foreach ($expansion->AllCardsWithStockAndItems as $product) {
            foreach ($product->stock as $stock){
                foreach($stock->items as $item) {
                    $soldPrice += $item->price * $item->quantity;
                }
            }
        }
        return view('expansion.show', compact('expansion', 'soldPrice'));
    }

    public function changeUpdate($id) {
        $expansion = $this->expansionRepository->getById($id);
        $expansion->update = 1;
        $expansion->save();
        foreach($expansion->AllCardsWithStock as $product) {
            if (!$product->update) {
                $product->update = 1;
                $product->save();
            }
            foreach($product->stock as $stock) {
                if (!$stock->update && $stock->quantity > 0) {
                    $stock->update = 1;
                    $stock->save();
                }
            }
        }

        return $this->index($expansion->idGame, $expansion->type);
    }
}
