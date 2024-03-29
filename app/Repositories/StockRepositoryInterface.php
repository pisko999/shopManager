<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 20/03/2019
 * Time: 12:50
 */

namespace App\Repositories;

use App\Http\Requests\StockAddRequest;
use App\Models\AllProduct;
use App\Models\Stock;
use App\Objects\StockFileItem;
use App\Services\MKMService;
use Illuminate\Http\Request;


interface StockRepositoryInterface extends ModelRepositoryInterface
{
    //public function whereInPaginate($list, $n);

    //public function getByProductIds($product_ids);

    public function addFromBuy($item);

    public function addFromMKM($item, $is_new);

    public function addFromCSV($item);

    public function addFromCSV2(StockFileItem $item);

    public function checkFromCSV($item);

    public function addItem(AllProduct $product, StockAddRequest $request);

    public function changePrice(Stock $stock, $price);

    public function changeState(Stock $stock, $state);

    public function increaseStock(Stock $stock, $quantity);

    public function decreaseStock(Stock $stock, $quantity);

    public function removeItemFromExistsExact(Stock $stock, $data);

    public function removeItemFromExists($product, $stockId);

    public function getInStock();

    public function getInStockInArray($ids);

    public function getInStockNotInArray($ids);

    public function differentUpdate(Stock $item, StockFileItem $mkmItem);

    public function getByIdArticleMKM($id);
    public function getByValues($data);
    public function getStock(Request $request);
}
