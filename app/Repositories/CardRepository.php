<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 16/03/2019
 * Time: 13:58
 */

namespace App\Repositories;

use App\Http\Requests\CardAddJsonRequest;
use App\Http\Requests\CardSearchRequest;
use App\Models\Card;
use App\Models\AllProduct;
use App\Models\Expansion;

use App\Repositories\StockRepository;
use Illuminate\Http\Request;

class CardRepository extends ProductModelRepository implements CardRepositoryInterface
{
    protected $stockRepository;

    public function __construct(StockRepositoryInterface $stockRepository, Card $card)
    {
        $this->stockRepository = $stockRepository;
        $this->model = $card;
    }

    public function getCardsByEditionAndColor($edition, $color){
        $q = $this->model;
        $q = $this->searchByEdition($q, $edition);
        $q = $this->searchByColor($q, array($color));
        return $q->get();
    }


    public function add(Request $request, AllProduct $product, Expansion $set)
    {
        $raritiesRepository = new RaritiesRepository();

        $rarities = $raritiesRepository->getRaritiesToShort();
        $colors = array('W' => 1, 'U' => 2, 'B' => 3, 'R' => 4, 'G' => 5);

        if (!isset($request->flavor_text))
            $request->flavor_text = "";

        if (!isset($request->mana_cost))
            $request->mana_cost = "";

        if (!isset($request->oracle_text))
            $request->oracle_text = "";

        if (!isset($request->collector_number))
            $request->collector_number = 0;
//        \Debugbar::info($product);
//        \Debugbar::info($product->id);

        //remove {} from manacost
        $to_replace = array("{", "}");
        str_replace($to_replace, "", $request->mana_cost);

        $card = new Card([
            'id' => $product->id,
            'rarity' => $rarities[$request->rarity],
            'number' => $request->collector_number,
            'mana_cost' => $request->mana_cost,
            'text' => $request->oracle_text,
            'flavor' => $request->flavor_text,
            'foil' => $request->isfoil,
            'exists_foil' => ($request->foil == 'true'),
            'exists_nonfoil' => ($request->nonfoil == 'true'),
        ]);
        $set->cards()->save($card);
        $product->card()->save($card);


        if (isset($request->colors))
            foreach ($request->colors as $color) // add new colorRepository
                \DB::table('card_color')->insert([
                    'card_id' => $card->id,
                    'color_id' => $colors[$color],
                ]);

        return $card;
    }

    public function getCardsByEditionPaginate($editionId, $n, $orderBy = "scryfallCollectorNumber", $orderByType = "desc", $page = 1, $foil = 0) // products.base_price desc
    {
        $q = $this->getCardsByEdition($editionId);
        //$q = $this->searchByFoil($q, $foil);
        $q = $this->searchByLang($q, 'en');
        $q = $this->joinData($q);
        return $q->orderBy($orderBy, $orderByType)
            ->paginate($n, ['*'], 'page', $page);
    }

    public function getCardsByEditionOnlyStockPaginate($editionId, $n, $orderBy = "scryfallCollectorNumber", $orderByType = "desc", $page = 1, $foil = 0) // products.base_price desc
    {
        $q = $this->getCardsByEdition($editionId);
        $q = $this->searchOnlyInStock($q);
        $q = $this->searchByLang($q, 1);
        $q = $this->joinData($q);
        return $q->orderBy('usd_price', 'desc')->orderByRaw('LENGTH(scryfallCollectorNumber) asc')
            ->paginate($n, ['*'], 'page', $page);
    }

    public function getCardsByEditionWithoutFoilPaginate($editionId, $n, $orderBy = "scryfallCollectorNumber", $orderByType = "desc", $page = 1, $foil = 0) // products.base_price desc
    {
        $q = $this->getCardsByEdition($editionId);
        $q = $this->searchByFoil($q, $foil);
        $q = $this->searchByLang($q, 'en');
        $q = $this->joinData($q);
        return $q->orderBy($orderBy, $orderByType)
            ->paginate($n, ['*'], 'page', $page);
    }

    public function getCardsByEditionOnlyStockWithProductAndStock($editionId)
    {
        $q = $this->getCardsByEdition($editionId);
        $q = $this->searchOnlyInStock($q);
        $q = $this->joinDataSmall($q);
        return $q->get();
    }


    private function getCardsByEdition($editionId) // products.base_price desc
    {
        $q = $this->model;
        $q = $this->searchByEdition($q, $editionId);
        return $q;
    }

    public function getCardsByEditionGet($editionId) // products.base_price desc
    {
        $q = $this->getCardsByEdition($editionId);
        $q = $this->joinData($q);
        return $q->get();
    }

    public function getCardByNameAndEdition($cardName, $edition_id)
    {
        $q = $this->model;
        $q = $this->searchByEdition($q, $edition_id);
        $q = $this->searchByName($q, $cardName);
        return $q->get();
    }

    public function getCardsByEditionWithProductAndStock($edition_id)
    {
        $q = $this->model;
        $q = $this->searchByEdition($q, $edition_id);
        $q = $this->joinData($q);
        $q = $q->orderby('scryfallCollectorNumber');
        return $q->get();

    }

    public function getCardsByEditionWithProduct($edition_id)
    {
        $q = $this->model;
        $q = $this->searchByEdition($q, $edition_id);
        $q = $this->searchByLang($q, 'en');
        $q = $this->joinProduct($q);
        $q = $q->orderby('scryfallCollectorNumber');
        return $q->get();

    }

    public function getCardsByEditionWithProductAndColorsWithoutFoil($edition_id)
    {
        $q = $this->model;
        $q = $this->searchByEdition($q, $edition_id);
        $q = $this->searchByLang($q, 1);
        $q = $this->searchByFoil($q, 0);
        $q = $this->joinProductAndColors($q);
        $q = $q->orderby('scryfallCollectorNumber');
        return $q->get();

    }

    public function getCardsByEditionAndFoilWithProductAndStock($edition_id, $foil)
    {
        $q = $this->model;
        $q = $this->searchByEdition($q, $edition_id);
        $q = $this->searchByLang($q, 'en');
        $q = $this->searchByFoil($q, $foil);
        $q = $this->joinData($q);
        $q = $q->orderby('scryfallCollectorNumber');
        return $q->get();

    }

    public function getCardsSearchPaginate(CardSearchRequest $request, $nbrPerPage)
    {
        $q = $this->model;

        if (isset($request->searchText) && $request->searchText != "")
            $q = $this->searchByName($q, $request->searchText);

        if (isset($request->lang) && $request->lang != "0")
            $q = $this->searchByLang($q, $request->lang);

        if (isset($request->onlyStock))// && $request->onlyStock)
            $q = $this->searchOnlyInStock($q, $request->onlyStock);

        if (isset($request->edition) && $request->edition != 0)
            $q = $this->searchByEdition($q, $request->edition);
        //\Debugbar::info($q->get());

        if (isset($request->color)) {
            $q = $this->searchByColor($q, $request->color);
        }

        if (isset($request->foil))
            $q = $this->searchByFoil($q, $request->foil);

        if (isset($request->rarity))
            $q = $this->searchByRarity($q, $request->rarity);

        $q = $this->joinData($q);

        $q = $q->orderBy('usd_price', 'desc')->orderByRaw('LENGTH(scryfallCollectorNumber) asc');


        $results = $q->paginate($nbrPerPage)->appends(request()->except('page'));
        return $results;
    }

    public function getByIdWithProductAndStock($id)
    {
        $q = $this->model;
        $q = $q->where('cards.id', '=', $id);
        $q = $this->joinDataSmall($q);
        return $q->first();
    }

    // private methods

    private function searchByEdition($q, $edition)
    {

        $q = $q->where(function ($q) use ($edition) {
            return $q->whereHas('product', function ($q) use ($edition) {
                return $q->where('idExpansion', '=', $edition);
            });
        });

        return $q;
    }

    private function searchByFoil($q, $foil)
    {
        if ($foil == -1)
            return $q;

        $q = $q->where(function ($q) use ($foil) {
            return $q->whereHas('stock', function ($q) use ($foil) {
                return $q->where('isFoil', '=', $foil);
            });
        });
        return $q;
        /*
        if ($foil == 0)
            return $q->whereFoil(0);
        if ($foil == 1)
            return $q->whereFoil(1);
        */
    }


    private function searchByRarity($q, $rarity)
    {
        if (in_array($rarity, ['C', 'U', 'R', 'M']))
            return $q->whereHas('rarity', function ($q) use ($rarity) {
                return $q->where('sign', '=', $rarity);
            });
        return $q;
    }

    private function joinDataSmall($q)
    {
        $q = $q->join('all_products', 'cards.id', '=', 'all_products.id')
            ->with('product.stock');
        return $q;
    }

    private function joinProductAndColors($q)
    {
        $q = $q->join('all_products', 'cards.id', '=', 'all_products.id')
            ->with('colors');
        return $q;
    }

    private function joinData($q)
    {
        //jointure because ordering on collumn in products table
        $q = $q->join('all_products', 'cards.id', '=', 'all_products.id')
            ->with('product.expansion')
            ->with('product.image')
            ->with('stock')
            ->with('stock.image')
            ->with('colors');

        return $q;
    }

    private function searchByName($q, $name)
    {
        $q = $q->where(function ($q) use ($name) {
            return $q->whereHas('product', function ($q) use ($name) {
                return $q->where('name', 'like', '%' . $name . "%");
            });
        });

        return $q;
    }

    private function searchByLang($q, $lang)
    {
        $q = $q->where(function ($q) use ($lang) {
            return $q->whereHas('product', function ($q) use ($lang) {
                return $q->whereHas('stock', function ($q) use ($lang) {
                    return $q->where('language', $lang);
                });
            });
        });

        return $q;
    }

    private function searchOnlyInStock($q)
    {
        $q = $q->where(function ($q) {
            return $q->whereHas('product', function ($q) {
                return $q->whereHas('stock', function ($q) {
                    return $q->where('quantity', '>', 0);
                });
            });
        });

        return $q;
    }

    private function searchByColor($q, $colors)
    {
        $q = $q->where(function ($q) use ($colors) {
            $i = 0;
            foreach ($colors as $color) {

                switch ($color) {
                    case "multi":
                        if ($i == 0)
                            $q = $q->has('colors', '>', 1);
                        else
                            $q = $q->orHas('colors', '>', 1);

                        break;
                    case "colorless":
                        if ($i == 0)
                            $q = $q->has('colors', '<', 1);
                        else
                            $q = $q->orHas('colors', '<', 1);
                        break;
                    default:
                        if ($i == 0)
                            $q = $q->has('colors', '=', 1)
                                ->whereHas('colors', function ($query) use ($color) {
                                    $query->where('name', '=', $color);

                                });

                        else
                            $q = $q->orHas('colors', '=', 1)
                                ->whereHas('colors', function ($query) use ($color) {
                                    $query->where('name', '=', $color);

                                });
                        break;

                }
                $i++;
            }
            return $q;
        });

        return $q;
    }


    /*
    public function test()
    {
        return $this->model->where("number", "regexp", "[^0-9]+")->with('product', 'edition')->get();
    }

    public function test2()
    {
        $cards = $this->model->where("number", "regexp", "s$")->with('product', 'edition')->get();
        foreach ($cards as $card) {
            for ($i = 0; $i < strlen($card->number); $i++)
                if (!is_numeric($card->number[$i]))
                    echo $card->number[$i];
            //$card->number = str_replace("s", "", $card->number);
            //$card->promo = 's';
            //$card->save();
        }
        return $cards;
    }
    */

    // maybe old code
    /*
    public function getCardsSearchByColorPaginate($color, $nbrPerPage)
    {
        return $this->getCardsSearchByColor($color)->paginate($nbrPerPage)->appends(request()->except('page'));
    }

    public function getCardsSearchByColor($color)
    {
        switch ($color) {
            case "multi":

                return $this->model->has('colors', '>', 1);
                break;
            case "colorless":

                return $this->model->has('colors', '<', 1);
                break;
            default:

                return $this->model
                    ->has('colors', '=', 1)
                    ->whereHas('colors', function ($query) use ($color) {
                        $query->where('color', '=', $color);

                    });
                break;

        }
    }
*/

}
