<?php

namespace App\Http\Controllers;

use App\Repositories\BuyCommandRepositoryInterface;
use App\Repositories\BuyItemRepositoryInterface;
use App\Repositories\StatusRepositoryInterface;
use App\Services\messagerieService;
use App\Services\StockService;
use Illuminate\Http\Request;

class BuyCommandConfirmController extends Controller
{
    private $stockService;
    private $buyCommandRepository;
    private $statusRepository;
    private $buyItemRepository;

    public function __construct(StockService $stockService, BuyCommandRepositoryInterface $buyCommandRepository, StatusRepositoryInterface $statusRepository, BuyItemRepositoryInterface $buyItemRepository)
    {
        $this->middleware('auth');
        $this->stockService = $stockService;
        $this->buyCommandRepository = $buyCommandRepository;
        $this->statusRepository = $statusRepository;
        $this->buyItemRepository = $buyItemRepository;
    }

    public function close($id)
    {
        $buyCommand = $this->buyCommandRepository->getById($id);
        if (!$buyCommand) {
            messagerieService::errorMessage("Command not found.");

        } else {
            $buyCommand->initial_value = $buyCommand->value();
            $buyCommand->save();
            $this->statusRepository->updateStatus($buyCommand->status, "closed");
            messagerieService::successMessage("Command closed.");
        }
        return redirect()->route('buyCommand.index');
    }

    public function checkQuantity($id)
    {
        $buyCommand = $this->buyCommandRepository->getById($id);
        $items = collect();

        foreach ($buyCommand->items as $item) {
            $quantity = $item->quantity;
            $max = $item->isFoil ? 4 : 20;

            foreach ($item->product->stock->where('isFoil', $item->isFoil) as $stock) { //TODO: save stockQuantity to AllProducts table
                $quantity += $stock->quantity;
            }

            if ($quantity > $max) {
                $item->totalQuantity = $quantity;
                $items->push($item);
            }
        }
        return view('buyCommand.quantityOver', compact('buyCommand', 'items'));
    }

    public function removeOverQuantity($id, Request $request)
    {
        //\Debugbar::info($request->all());
        if (!\Storage::exists('removes'))
            \Storage::makeDirectory('removes');
        $filename = 'removes/' . $id . '-' . time() . '.data';
        foreach ($request->all() as $key => $value) {
            if (str_contains($key, 'chckRemove')) {
                $idItem = substr($key, 10);
                $item = $this->buyItemRepository->getById($idItem);
                $quantityToRemove = $value - ($item->isFoil ? 4 : 20); // TODO: should use repository
                \Storage::append($filename, (($quantityToRemove > $item->quantity) ? $item->quantity : $quantityToRemove) . 'x ' . $item->card->expansion->sign . '-' . $item->card->scryfallCollectorNumber . ' - ' . ($item->isFoil ? 'F ' : '') . $item->product->name);
                if ($item->quantity > $quantityToRemove) {
                    $item->quantity -= $quantityToRemove;
                    $item->save();
                } else {
                    $item->delete();
                }
                //\Debugbar::info($id);
            }
        }
        return \Storage::download($filename);
    }

    public function showStocking($id){
        $buyCommand = $this->buyCommandRepository->getById($id);
        $album = $this->buyItemRepository->getByStocking($buyCommand,2);
        $center = $this->buyItemRepository->getByStocking($buyCommand,1);
        $box = $this->buyItemRepository->getByStocking($buyCommand,0);

        return view('buyCommand.showStocking', compact('buyCommand','album','center','box'));
    }
}
