<?php

namespace App\Http\Controllers;

use App\Models\AllProduct;
use App\Models\Expansion;
use App\Models\GiftItem;
use App\Models\GiftList;
use App\Repositories\ExpansionRepository;
use App\Repositories\ExpansionRepositoryInterface;
use App\Repositories\GiftItemRepositoryInterface;
use App\Repositories\GiftListRepositoryInterface;
use App\Repositories\GiftRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GiftListController extends Controller
{
    private GiftListRepositoryInterface $giftListRepository;
    private GiftItemRepositoryInterface $giftItemRepository;
    private GiftRepositoryInterface $giftRepository;
    private ExpansionRepositoryInterface $expansionRepository;
    public function __construct(
        GiftListRepositoryInterface $giftListRepository,
        ExpansionRepositoryInterface $expansionRepository,
        GiftItemRepositoryInterface $giftItemRepository,
        GiftRepositoryInterface $giftRepository
    )
    {
        $this->giftListRepository = $giftListRepository;
        $this->expansionRepository = $expansionRepository;
        $this->giftItemRepository = $giftItemRepository;
        $this->giftRepository = $giftRepository;
    }

    public function index() {
        $giftLists = GiftList::All();
        return view('giftList.index',compact('giftLists'));
    }

    public function create(Request $request){
        $giftList = $this->giftListRepository->add($request->input('name'));
        return json_encode($giftList);
    }

    public function show($id) {
        ini_set('memory_limit', '1000000000');
        $giftList = GiftList::with('giftItems', 'giftItems.product', 'giftItems.product.expansion', 'giftItems.product.priceGuide')->find($id);
        $expansions = $this->expansionRepository->getArrayForSelect();
        return view('giftList.show', compact('giftList', 'expansions'));
    }
    public function showGifts($id)
    {
        $giftList = $this->giftListRepository->getById($id);
        $gifts = $this->giftRepository->getByGiftList($id);
        return view('giftList.showGifts',compact('giftList', 'gifts'));
    }

    public function delete($id): bool {
        $giftList = GiftList::find($id);
        if ($giftList){
            return json_encode($giftList->delete());
        }
        return false;
    }

    public function deleteItem($id): bool{
        $giftItem = GiftItem::find($id);
        if ($giftItem){
            return $giftItem->delete();
        }
        return false;
    }

    public function showAddByExpansion($id, Request $request): bool|View {
        ini_set('memory_limit','512M');
        $expansion = Expansion::where(['idMkm' => $request->input('idExpansion')])->first();
        $products = AllProduct::where(['idExpansion' => $request->input('idExpansion'), 'idCategory' => 1])->with('card', 'card.rarity', 'priceGuide')->get()->sortBy('card.scryfallCollectorNumber');
        $foil = $request->input('foils');
        return view('giftList.showByExpansion', compact('id', 'expansion', 'products', 'foil'));
    }

    public function addProduct($id, $idProduct, Request $request){
        $giftList = GiftList::find($id);
        $foil = $request->input('foil') === 'true' ? 1 : 0;
        $existing = $giftList->giftItems->where('all_product_id', $idProduct)->where('foil', $foil)->first();
        if ($existing) {
            $existing->quantity++;
            $existing->quantity_rest++;
            $existing->save();
            return $existing;
        }
        $product = AllProduct::find($idProduct);
        if ($giftList && $product) {
            $newGiftItem = new GiftItem(['quantity' => $request->input('quantity'), 'quantity_rest' => $request->input('quantity'), 'foil' => $foil]);
            $newGiftItem->giftList()->associate($giftList);
            $newGiftItem->product()->associate($product);
            $newGiftItem->save();
            return $newGiftItem;
        }
        return false;
    }
}
