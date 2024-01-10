<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 21/03/2019
 * Time: 16:59
 */

namespace App\Repositories;

use App\Http\Requests\ItemAddRequest;
use \App\Models\Item;
use \App\Models\Command;
use App\Models\Stock;
use App\Services\StockService;
use http\Env\Request;

class ItemRepository extends ModelRepository implements ItemRepositoryInterface
{
    private $stockRepository;
    private $stockService;

    public function __construct(Item $item, StockRepositoryInterface $stockRepository, StockService $stockService)
    {
        $this->model = $item;
        $this->stockRepository = $stockRepository;
        $this->stockService = $stockService;
    }

    public function stores(Request $request, Command $command)
    {

        $item = $this->model->where('stock_id', $request->stock_id)->where('command_id', $command->id)->where('price', $request->price)->first();

        if ($item == null)
            $item = $this->getNewItem($request, $command);

        $item = $this->increaseQuantity($item, $request->quantity);

        return $item;
    }

    public function increase($id, $quantity)
    {
        $item = $this->getById($id);
        if ($item != null)
            return $this->increaseQuantity($item, $quantity);
        return false;
    }

    public function decrease($id, $quantity)
    {
        $item = $this->getById($id);

        if ($item == null)
            return false;

        //if ($item->quantity <= $quantity)
        $item->quantity -= $quantity;
        $item->stock->quantity += $quantity;
        //$this->stockService->increase($item->stock, $quantity);

        if ($item->quantity <= 0)
            $item->delete();
        else
            $item->save();

        //if ($quantity > 0)
        //$this->delete($id, $quantity);
        return true;

    }

    private function getNewItem(Request $request, Command $command)
    {
        $item = new $this->model([
            'stock_id' => $request->stock_id,
            'command_id' => $command->id,
            'price' => $request->price,
            'quantity' => 0,
        ]);

        $command->items()->save($item);

        return $item;
    }

    private function increaseQuantity(Item $item, $quantity)
    {
        if ($item->stock->quantity < $quantity)
            $quantity = $item->stock->quantity;

        $item->quantity += $quantity;
        $item->save();
        $item->stock->quantity -= $quantity;
//        $this->stockService->decrease($item->stock, $quantity);

        return $item;

    }

    public function storeFromMKM($data, $command, $updateStock)
    {
        \DB::beginTransaction();
        foreach ($data as $article) {
            $stock = $this->stockRepository->getByValues($article);

            echo $article->idProduct . ', ' . $article->product->enName . " isNew: " . ($stock->is_new ?? 'nema') . "\n";

            $stockNew = $this->stockRepository->addFromMKM($article, $stock->is_new ?? 0);

            $item = $this->createFromMKM($article, $stockNew, $command);
            if ($updateStock && $stock != null) {
                $stock->quantity -= $article->count;
                if ($stock->quantity < 0)
                    $stock->quantity = 0;
                $stock->save();
            }
        }

        \DB::commit();
    }

    public function createFromMKM($data, $stock, $command)
    {
        return $this->model->firstOrCreate([
            'stock_id' => $stock->id,
            'command_id' => $command->id,
            'price' => $data->price,
            'quantity' => $data->count,
        ]);
    }

    public function relistItems($items)
    {
        foreach ($items as $item) {
            $stock = $item->stock;
            if (!$stock)
                return false;
            $this->stockService->increase($stock, $item->quantity);
        }
    }

}
