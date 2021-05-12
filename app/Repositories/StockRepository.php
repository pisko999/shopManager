<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 20/03/2019
 * Time: 12:51
 */

namespace App\Repositories;

use App\Http\Requests\StockAddRequest;
use App\Libraries\Prices;
use App\Models\Image;
use App\Models\Image_stock;
use App\Models\Language;
use App\Models\Stock;
use App\Models\Product;
use App\Objects\StockFileItem;
use App\Services\MKMService;

class StockRepository extends ModelRepository implements StockRepositoryInterface
{
    private $languagesIds;

    public function __construct(Stock $stock)
    {
        $this->model = $stock;
        $this->languagesIds = collect();
    }

    /*
        public function whereInPaginate($list, $n)
        {
            return $this->model->where('state', 'NM')->whereOr('state', 'M')->whereIn('product_id', $list)->paginate($n);
        }


        public function getByProductIds($product_ids)
        {
            $items = $this->model->whereIn('product_id', $product_ids)->get();
            return $items;
        }
    */
    public function addFromBuy($item)
    {
        $stocks = $this->model->where('all_product_id', $item->id_product)->where('isFoil', $item->isFoil)->where('language_id', $item->id_language)->where('state', isset($item->condition) ? $item->condition : "NM")->orderBy('price')->get();//TODO:add other criteria as signed
        if ($stocks->count() == 0)
            $stock = $this->newFromBuy($item);
        else {
            $stock = $stocks->first();
            $stock->quantity += $item->quantity;

            $quantity = 0;
            foreach ($stocks as $stock)
                $quantity += $stock->quantity;
            if ($quantity > 20) {
                $stock->quantity -= $quantity - 20;
                $stock->save();
                $stock->error = $quantity;
            } else
                $stock->save();


        }

        $item->Stock()->associate($stock)->save();

        return $stock;
    }

    private function newFromBuy($item)
    {
        $price = $item->price * 1.6;
        if ($price < 0.16)
            $price = 0.16;
        $stocking = $price >= 1.98 ? 2 : ($price >= 0.98 ? 1 : 0);
        return $this->model->create([
            'all_product_id' => $item->id_product,
            'initial_price' => $item->price,
            'quantity' => $item->quantity,
            'price' => $price,
            'stock' => $stocking,
            'language_id' => $item->id_language,
            'isFoil' => $item->isFoil,
            'signed' => $item->signed,
            'playset' => $item->playset,
            'altered' => $item->altered,
            'state' => $item->state
        ]);
    }

    public function addFromMKM($data)
    {
        $stock = $this->model->firstOrCreate(
            [
                'idArticleMKM' => $data->idArticle,
                'all_product_id' => $data->idProduct
            ], [
                'initial_price' => $data->price,
                'quantity' => 0,
                'price' => $data->price,
                'stock' => $data->price > 1.97 ? 3 : ($data->price > 0.97 ? 2 : 1),
                'language_id' => $data->language->idLanguage,
                'isFoil' => isset($data->isFoil) ? $data->isFoil : null,
                'signed' => isset($data->isSigned) ? $data->isSigned : null,
                'playset' => isset($data->isPlayset) ? $data->isPlayset : null,
                'altered' => isset($data->isAltered) ? $data->isAltered : null,
                'state' => isset($data->condition) ? $data->condition : "NM",
                'comments' => $data->comments,
                'modifiedMKM' => isset($data->lastEdited) ? $data->lastEdited : null,
            ]
        );

        return $stock;
    }

    public function addFromCSV($item)
    {
        if (!$this->languagesIds->contains($item[7])) {
            $lang = Language::firstOrCreate(['id' => $item[7]]);
            $this->languagesIds->add($item[7]);
        }
        if ($item[6] < 0.98)
            $stocking = 1;
        elseif ($item[6] < 1.98)
            $stocking = 2;
        else
            $stocking = 3;
        $stock = Stock::firstOrCreate(
            [
                'idArticleMKM' => $item[0],
                'all_product_id' => $item[1]
            ], [
                'language_id' => $item[7],
                'comments' => $item[13],
                'initial_price' => $item[6],
                'price' => $item[6],
                'quantity' => $item[14],
                'state' => $item[8],
                'isFoil' => $item[9] == "" ? false : true,
                'signed' => $item[10] == "" ? false : true,
                'playset' => $item[11] == "" ? false : true,
                'altered' => $item[12] == "" ? false : true,
                'stock' => $stocking
            ]
        );

        return $stock;
    }

    public function addFromCSV2(StockFileItem $item)
    {
        if (!$this->languagesIds->contains($item->language)) {
            $lang = Language::firstOrCreate(['id' => $item->language]);
            $this->languagesIds->add($item->language);
        }
        if ($item->price < 0.98)
            $stocking = 1;
        elseif ($item->price < 1.98)
            $stocking = 2;
        else
            $stocking = 3;
        $stock = Stock::firstOrCreate(
            [
                'idArticleMKM' => $item->idArticle,
                'all_product_id' => $item->idProduct
            ], [
                'language_id' => $item->language,
                'comments' => $item->comments,
                'initial_price' => $item->price,
                'price' => $item->price,
                'quantity' => $item->amount,
                'state' => $item->condition,
                'isFoil' => $item->foil == "" ? false : true,
                'signed' => $item->signed == "" ? false : true,
                'playset' => $item->playset == "" ? false : true,
                'altered' => $item->altered == "" ? false : true,
                'stock' => $stocking
            ]
        );

        return $stock;
    }

    public function checkFromCSV($item)
    {
        $stock = Stock::where('idArticleMKM', '=', $item[0])->first();
        if ($stock == null) {
            $this->addFromCSV($item);

        }

        $changed = false;
        if ($stock->price != $item[6]) {
            $stock->price = $item[6];
            $changed = true;
        }
        if ($stock->language != $item[7]) {
            $stock->language = $item[7];
            $changed = true;
        }
        if ($stock->state != $item[8]) {
            $stock->state = $item[8];
            $changed = true;
        }
        if ($stock->isFoil != ($item[9] == "" ? false : true)) {
            $stock->isFoil = ($item[9] == "" ? false : true);
            $changed = true;
        }
        if ($stock->signed != ($item[10] == "" ? false : true)) {
            $stock->signed = ($item[10] == "" ? false : true);
            $changed = true;
        }
        if ($stock->playset != ($item[11] == "" ? false : true)) {
            $stock->playset = ($item[11] == "" ? false : true);
            $changed = true;
        }
        if ($stock->altered != ($item[12] == "" ? false : true)) {
            $stock->altered = ($item[12] == "" ? false : true);
            $changed = true;
        }
        if ($stock->comments != $item[13]) {
            $stock->comments = $item[13];
            $changed = true;
        }
        if ($stock->quantity != $item[14]) {
            $stock->quantity = $item[14];
            $changed = true;
        }

        if ($changed) {
            $stock->save();
        }

        return $changed;
    }


    public function addItem(Product $product, $request)
    {

        //$request['quantity']
        //$request['foil']
        //$request['price']
        //$request['state']


        if ($request['quantity'] == 0 || $request['quantity'] == null)
            return;
        if (!isset($request->lang))
            $request->lang = "EN";
        $stock = $this->model->where('product_id', $product->id)->where('language', $request->lang)->get();
        //trying to add to exists
        //\Debugbar::info($stock);

        foreach ($stock as $s) {

            $ret = $this->addItemToExists($s, $request);
            if ($ret != false) {
                return $ret;
            }
        }

        return $this->addNewItem($product, $request);
    }

    private function addNewItem(Product $product, $request)
    {

        //adding new item
        $product->price != null ? $price = $product->price->m : $price = 0;

        if (isset($request['price']) && $request['price'] > 0) {
            $price = $request['price'];
        }
        $state = "NM";
        if (isset($request['state'])) {


            $state = $request['state'];
        }

        $item = new Stock([
            'product_id' => $product->id,
            'initial_price' => $price, //set back to $product->price->m as not work with items without initial prices
            'quantity' => $request['quantity'],
            'price' => $price,
            'state' => $state,
            'language_id' => mb_strtoupper($request['lang'])
        ]);

        $item->save();
//\Debugbar::info($request->image);
        if (isset($request->image) && $request->image != null) {
            $fileName = $item->id . '.' . $request->image->getClientOriginalExtension();
            //var_dump($categoryRepository->getById($request->category));
            $storagePath = 'image/stock';
            $path = $request->image->storeAs('public/' . $storagePath, $fileName);
            \Debugbar::info($path);
            $image = new Image_stock([
                'path' => $storagePath . '/' . $fileName,
                'alt' => $product->name,
            ]);
            $item->image()->save($image);
        }
        return $item;
    }

    private function addItemToExists(Stock $stock, $request)
    {
        //var_dump($data['state']);
        //\Debugbar::info($data);
        //\Debugbar::info($stock->foil);

        if (($request['price'] == null || $stock->price == $request['price']) && $stock->state == $request['state']) {
            $stock->quantity += $request['quantity'];
            $stock->save();
            return $stock;
        }
        return false;
    }

    public function changePrice(Stock $stock, $price)
    {
        $stock->price = $price;
        $stock->save();
    }

    public function changeState(Stock $stock, $state)
    {
        $stock->state = $state;
        $stock->save();
    }

    public function increaseStock(Stock $stock, $quantity)
    {
        $stock->quantity += $quantity;
        $stock->save();
    }

    public function decreaseStock(Stock $stock, $quantity)
    {
        \Debugbar::info($quantity);

        $stock->quantity -= $quantity;
        $idArticleMKM = $stock->idArticleMKM;
        if ($stock->quantity <= 0) {
            $stock->quantity = 0;
            $stock->idArticleMKM = null;
        }
        $stock->save();

        if ($stock->quantity == 0 && count($stock->items) == 0)
            $stock->delete();
        return $idArticleMKM;
    }

    public function removeItemFromExistsExact(Stock $stock, $data)
    {
        //var_dump($data['state']);

        if (($data['price'] == null || $stock->price == $data['price']) && $stock->state == $data['state']) {
            //var_dump($data);
            $stock->quantity -= $data['quantity'];
            $stock->save();
            return true;
        }
        return false;
    }

    public function removeItemFromExists($product, $stockId)
    {
        $stock = $this->model->whereId($stockId)->first();
        if ($stock->quantity > $product->quantity) {
            $stock->quantity -= $product->quantity;
            $product->quantity = 0;
        } else {
            $product->quantity -= $stock->quantity;
            $stock->quantity = 0;
        }
        $stock->save();
    }

    public function getInStock()
    {
        return $this->model->where('Quantity', '>', 0)->get();
    }

    public function getInStockInArray($ids)
    {
        return $this->model->where('Quantity', '>', 0)->whereIn('idArticleMKM', $ids);
    }

    public function getInStockNotInArray($ids)
    {
        return $this->model->where('Quantity', '>', 0)->whereNotIn('idArticleMKM', $ids);
    }

    public function differentUpdate(Stock $item, StockFileItem $mkmItem)
    {
        //making collection for saving each change
        $changed = collect();

        //if article with saved id has different id of product it is error
        if ($item->all_product_id != $mkmItem->idProduct) {
            $changed->push(['type' => 'productError', [$item->id, $mkmItem->idArticle]]);
            return $changed;
        }

        //quantity
        if ($item->quantity != $mkmItem->amount) {
            $changed->push(['type' => 'quantity', [$item->id, $mkmItem->idArticle, $item->quantity - $item->amount]]);
            $item->quantity = $mkmItem->amount;
        }

        //price
        if (floatval($item->price) != floatval($mkmItem->price)) {
            $changed->push(['type' => 'price', [$item->id, $mkmItem->idArticle, floatval($item->price), floatval($mkmItem->price)]]);
            $item->price = floatval($mkmItem->price);
        }

        //condition
        if ($item->state != $mkmItem->condition) {
            $changed->push(['type' => 'state', [$item->id,$mkmItem->idArticle, $item->state]]);
            $item->state = $mkmItem->condition;
        }

        //comments
        if ($item->comments != $mkmItem->comments) {
            $changed->push(['type' => 'comments', [$item->id, $mkmItem->idArticle, $item->comments]]);
            $item->comments = $mkmItem->comments;
        }

        //is on sale
        if ($item->on_sale != $mkmItem->onSale) {
            $changed->push(['type' => 'onSale', [$item->id, $mkmItem->idArticle, $item->on_sale]]);
            $item->on_sale = $mkmItem->onSale;
        }

        //if no changes -> return
        if ($changed->count() == 0)
            return null;

        //else saving item and return collection of changes
        $item->save();
        return $changed;
    }

    public function getByIdArticleMKM($id)
    {
        return $this->model->where('idArticleMKM', '=', $id)->first();
    }

    public function getByValues($data)
    {
        return $this->model->where([
            'all_product_id' => $data->idProduct,
            'price' => $data->price,
            'language_id' => $data->language->idLanguage ?? $data->lang,
            'isFoil' => isset($data->isFoil) ? boolval($data->isFoil) : null,
            'signed' => isset($data->isSigned) ? boolval($data->isSigned) : null,
            'playset' => isset($data->isPlayset) ? boolval($data->isPlayset) : null,
            'altered' => isset($data->isAltered) ? boolval($data->isAltered) : null,
            'state' => isset($data->condition) ? $data->condition : "NM",
        ])->first();
    }

}
