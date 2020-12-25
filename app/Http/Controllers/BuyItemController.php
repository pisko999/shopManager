<?php

namespace App\Http\Controllers;

use App\BuyItem;
use App\Repositories\BuyCommandRepositoryInterface;
use App\Repositories\BuyItemRepositoryInterface;
use Illuminate\Http\Request;

class BuyItemController extends Controller
{
    private $buyItemRepository;
    private $buyCommandRepository;

    public function __construct(BuyItemRepositoryInterface $buyItemRepository, BuyCommandRepositoryInterface $buyCommandRepository)
    {
        $this->middleware('auth');

        $this->buyItemRepository = $buyItemRepository;
        $this->buyCommandRepository = $buyCommandRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function add(Request $request)
    {
        $buyCommand = $this->buyCommandRepository->getById($request->buyCommandId);
        if ($buyCommand)
            return response('', 404);
        $buyItem = $this->buyItemRepository->add($buyCommand, $request->all());
        return $buyItem;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\BuyItem $buyItem
     * @return \Illuminate\Http\Response
     */
    public function show(BuyItem $buyItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\BuyItem $buyItem
     * @return \Illuminate\Http\Response
     */
    public function edit(BuyItem $buyItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\BuyItem $buyItem
     * @return \Illuminate\Http\Response
     */
    public function remove($id)
    {
        $answer = $this->buyItemRepository->remove($id);

        return $answer;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function update($id, Request $request)
    {
        if ($request->action == 'increase')
            $answer = $this->buyItemRepository->increase($id, $request->all());
        elseif ($request->action == 'decrease')
            $answer = $this->buyItemRepository->decrease($id, $request->all());
        elseif ($request->action == 'remove')
            $answer = $this->buyItemRepository->remove($id);
        else
            return "false1";

        return $answer;
    }
}
