<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockingPostRequest;
use App\Http\Requests\StockingShowRequest;
use App\Models\Expansion;
use App\Repositories\CardRepositoryInterface;
use App\Repositories\EditionRepositoryInterface;
use App\Repositories\ExpansionRepositoryInterface;
use App\Services\MKMService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockingController extends Controller
{
    private $expansionRepository;
    private $cardRepository;

    public function __construct(ExpansionRepositoryInterface $expansionRepository, CardRepositoryInterface $cardRepository)
    {
        $this->expansionRepository = $expansionRepository;
        $this->cardRepository = $cardRepository;
    }

    public function stockingList()
    {

        $editions = $this->expansionRepository->getArrayForSelect();
        $r = 'stockingEditionGet';
        $m = 'get';
        $requireFoilSelect = true;
        return view('editionSelectGet', compact('editions', 'r', 'm', 'requireFoilSelect'));
    }


    public function stockingEditionGet1(Request $request)
    {
        ini_set('memory_limit','512M');
        $expansion = $this->expansionRepository->getByMKMId($request->id);
        $cards = $expansion->AllCardsWithBasicRelations;
        $max = ceil($cards->count() / 5);
        $foil = $request->foils == 1;
        return view('showStocking2', compact('max', 'cards', 'expansion', 'foil'));
    }

    public function stockingEditionGet(Request $request)
    {
        $isCMR = $request->id == 3454;
        $expansion = $this->expansionRepository->getByMKMId($request->id);
        $cards = $expansion->AllCardsWithBasicRelations;
        if ($isCMR)
            $ar = ['non-color', 'white', 'blue', 'black', 'red', 'green', 'multicolor', 'artifact', 'lands', 'some']; //problem with battle for zendikar and other expansions with non-color cards
        else
            $ar = ['white', 'blue', 'black', 'red', 'green', 'multicolor', 'artifact', 'lands', 'some', 'd', 'l', 'k', 'h']; //problem with battle for zendikar and other expansions with non-color cards
        $col = collect();
        $max = collect();
        $i = 0;
        foreach ($ar as $item) {
            $max->put($item, 0);
            $col->put($item, collect());
        }
        //var_dump($col);
        foreach ($cards as $card) {

            if ($col[$ar[$i]]->count() > 0)
                if (substr($col[$ar[$i]]->last()->name, 0, 1) > substr($card->name, 0, 1)) {
                    $max[$ar[$i]] = $col[$ar[$i]]->count();
                    $i++;
                    if ($isCMR && $card->scryfallCollectorNumber == 62)
                        $i--;

                }
            //var_dump($i);
            //var_dump($card->name);
            $col[$ar[$i]]->push($card);
        }
        $max[$ar[$i]] = $col[$ar[$i]]->count();
        /*
                $c = $cards->groupBy(function ($item){
                    return $item->card->colors->count() == 0
                        && ($item->card->cardFaces->count() == 0 || $item->card->cardFaces->colors->count() == 0);
                }, $preserveKeys = true);
        */
        \Debugbar::info($max);

        $colors = array_slice($ar, $isCMR ? 1 : 0, 5);
        $maxColorCards = max(array_slice($max->toArray(), $isCMR ? 1 : 0, 5));
        return view('showStocking', compact('max', 'col', 'expansion', 'ar', 'maxColorCards', 'colors'));
    }

    public function stockingPost(StockingPostRequest $request)
    {

        $edition = $this->editionRepository->getById($request->edition);
        if ($edition == null)
            return abort(404);
        $cards = $edition->cards;
        foreach ($cards as $card) {
            if (count($card->product->stock) > 0)
                foreach ($card->product->stock as $stock) {
                    $stock->stock = $stock->price >= 25 ? ($stock->price >= 50 ? 3 : 2) : 1;
                    $stock->save();

                }
        }

        return redirect()->back();
    }

    public function stockingShowGet()
    {

        $editions = $this->editionRepository->getArrayForSelect();
        $r = 'admin.stockingShow';
        return view('admin.editionGet', compact('editions', 'r'));
    }

    public function stockingShow(StockingPostRequest $request)
    {
        $cards = $this->cardRepository->getCardsByEditionWithProductAndColorsWithoutFoil($request->edition);
        $maxCard = $this->cardRepository->getCardByNameAndEdition("Plains", $request->edition)->first();
        if ($maxCard != null)
            $cards = $cards->filter(function ($value) use ($maxCard) {
                return $value->number < $maxCard->number;
            });
        /*
                switch ($request->stock) {
                    case 1:
                        $cards = $allCards->filter(function ($value, $key) {
                            return $value->product->base_price < 25;
                        });
                        break;
                    case 2:
                        $cards = $allCards->filter(function ($value, $key) {
                            return $value->product->base_price >= 25 && $value->product->base_price < 50;
                        });
                        break;
                    case 3:
                        $cards = $allCards->filter(function ($value, $key) {
                            return $value->product->base_price >= 50;
                        });
                        break;
                }
        */

        $colors = ['White', 'Blue', 'Black', 'Red', 'Green'];
        $max = 0;
        foreach ($colors as $color) {
            $list[$color] = $cards->filter(function ($value) use ($color) {
                return count($value->colors) == 1 && $value->colors[0]->color == $color;
            })->values();
            if (count($list[$color]) > $max)
                $max = count($list[$color]);
        }

        $list["Multicolor"] = $cards->filter(function ($value) {
            return count($value->colors) > 1;
        })->values();

        $list["Colorless"] = $cards->filter(function ($value) {
            return count($value->colors) < 1;
        })->values();

        return view('admin.showStocking', compact('list', 'colors', 'max'));
    }
}
