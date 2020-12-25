<?php

namespace App\Http\Controllers;

use App\BuyCommand;
use App\Repositories\BuyCommandRepositoryInterface;
use App\Repositories\ExpansionRepositoryInterface;
use Illuminate\Http\Request;

class BuyCommandController extends Controller
{
    private $buyCommandsRepository;

    public function __construct(BuyCommandRepositoryInterface $buyCommandRepository)
    {
        $this->middleware('auth');
        $this->buyCommandsRepository = $buyCommandRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $buyCommands = $this->buyCommandsRepository->getPaginate();
        $links = $buyCommands->render();
        return view('buyCommand.index', compact('buyCommands', 'links'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\BuyCommand $buyCommand
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $buyCommand = $this->buyCommandsRepository->getById($id);
        if (!$buyCommand)
            return view('404');
        return view('buyCommand.show', compact('buyCommand'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\BuyCommand $buyCommand
     * @return \Illuminate\Http\Response
     */
    public function edit(BuyCommand $buyCommand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\BuyCommand $buyCommand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BuyCommand $buyCommand)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\BuyCommand $buyCommand
     * @return \Illuminate\Http\Response
     */
    public function destroy(BuyCommand $buyCommand)
    {
        //
    }

    public function editionSelect(ExpansionRepositoryInterface $expansionRepository)
    {
        $editions = $expansionRepository->getArrayForSelect();
        $r = 'buyCommandEditionGet';
        $m = 'get';
        $requireFoilSelect = true;
        return view('editionSelectGet', compact('editions', 'r', 'm', 'requireFoilSelect'));
    }

    public function editionGet(ExpansionRepositoryInterface $expansionRepository, Request $request)
    {
        $expansion = $expansionRepository->getByMKMId($request->id);

        if (!$expansion)
            return view('404');

        $cards = $expansion->allCardsWithRelationsPaginate();
        foreach ($cards as $card) {
            $stocks = $card->getStock($request->foils);
            $quantity = 0;
            foreach ($stocks as $stock)
                $quantity += $stock->quantity;

            $card->quantity = $quantity;

            $background = '';

            if (!isset($card->card)) {
                $background = "blue";
            } elseif ($card->card->rarity->sign == 'M')
                $background = 'red';
            elseif ($card->card->rarity->sign == 'R')
                $background = 'gold';
            elseif ($card->card->rarity->sign == 'U')
                $background = 'grey';

            if ($card->quantity >= ($request->foils ? 4 : 20))
                $background = 'green';

            $card->background = $background;


        }
        $links = $cards->render();

        return view('buyCommand.expansion', compact('expansion', 'cards', 'links'));
    }

    public function editionSave(ExpansionRepositoryInterface $expansionRepository, Request $request)
    {
        $this->buyCommandsRepository->saveEdition($request->all());

        $request['page'] += 1;

        return $this->editionGet($expansionRepository, $request);
    }

    public function editionMake($id, Request $request)
    {
        $buyCommand = $this->buyCommandsRepository->getById($id);
        if (!$buyCommand)
            return view('404');
        $total = 0;
        foreach ($buyCommand->items as $item) {
            var_dump($item->product->stock);
            if ($item->product->stock)
                $item->priceProviz = $item->product->Stock()->orderBy('price')->first()->price;
            else
                $item->priceProviz = $item->card->usd_price != null ? $item->card->use_price * 1.1 : 0;
            $total += $item->quantity * $item->priceProviz;

        }
        $perOne = $request->value / $total;
        foreach ($buyCommand->items as $item) {
            $item->price = $item->priceProviz * $perOne;
            unset($item->priceProviz);
            $item->save();

        }

        \Debugbar::info($request->all());
        return $this->show($id);
    }
}
