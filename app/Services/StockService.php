<?php


namespace App\Services;


use App\Models\Stock;
use App\Repositories\StockRepositoryInterface;

class StockService
{
    protected $MKM = true; //TODO:Remove
    protected $stockRepository;
    protected $MKMService;

    public function __construct(StockRepositoryInterface $stockRepository)
    {
        $this->stockRepository = $stockRepository;
        if ($this->MKM)
            $this->MKMService = new MKMService();
    }

    public function addFromBuy($buyItem, $presale = false)
    {
        $stock = $this->stockRepository->addFromBuy($buyItem);
        $doesnExist = false;
        if ($this->MKM) {
            if ($stock->idArticleMKM) {
                $mkmStock = $this->MKMService->increaseStock($stock->idArticleMKM, $buyItem->quantity);
                //var_dump($mkmStock);

                if (isset($mkmStock->error) && str_contains($mkmStock->error, " doesn't exist in stock")) {
                    $doesnExist = true;
                    goto ifArticleDoesntExistAnymore;
                }

                if (!isset($mkmStock->article[0])) {
                    $stock->error = $mkmStock;//->failed[0]->errorMessage;
                    var_dump($stock->error);
                    //$this->stockRepository->decreaseStock($stock, $mkmStock->failed[0]->count);
                } else {
                    $mkmStock = $mkmStock->article[0];


                    $stock->modifiedMKM = substr(str_replace('T', ' ', $mkmStock->lastEdited), 0, 16);
                    //TODO: use $session
                    if (isset($stock->error)) {
                        $error = $stock->error;
                        unset($stock->error);
                        $stock->save();
                        $stock->error = $error;
                    } else
                        $stock->save();
                    //end
                }
            } else {
                //$t = time();
                /*
                $product = $this->MKMService->getProduct($buyItem->id_product);

                if ($buyItem->isFoil)
                    $price = $product->product->priceGuide->TRENDFOIL;
                else
                    $price = $product->product->priceGuide->SELL;
                if ($price < 0.16)
                    $price = 0.16;
                */
                ifArticleDoesntExistAnymore:
                if ($doesnExist) {
                    echo "card id:" . $buyItem->id . " marked by idArticleMKM:" . $stock->idArticleMKM . " not exist on MKM";
                    var_dump($buyItem);
                }
                $priceGuide = $buyItem->product->priceGuide->first();
                $price = $priceGuide != null ?
                    \App\Libraries\PriceLibrary::getPrice(
                        $buyItem->isFoil ?
                            ($priceGuide->foilTrend + $priceGuide->foilAvgOne + $priceGuide->foilAvgSeven) / 3 :
                            ($priceGuide->trend + $priceGuide->avgOne + $priceGuide->avgSeven) / 3,
                        \App\Libraries\PriceLibrary::Eur,
                        \App\Libraries\PriceLibrary::Eur
                    )
                    :

                ($buyItem->isFoil ?
                    $price = $buyItem->card->usd_price_foil
                    :
                    $price = $buyItem->card->usd_price
                );
                $lowPriceName = $buyItem->isFoil ? 'foilLow' : 'lov';
                if ($priceGuide != null && $price < $priceGuide->$lowPriceName) {
                    $price = 1.2 * $priceGuide->$lowPriceName;
                }

                if ($price != null) {
                    $mkmStock = $this->MKMService->addToStock(
                        $buyItem->id_product,
                        $buyItem->quantity,
                        $price,
                        $buyItem->state,
                        $buyItem->id_language,
                        $buyItem->is_new? 'New' : '',
                        $buyItem->isFoil ? 'true' : 'false',
                        $buyItem->signed ? 'true' : 'false',
                        $buyItem->altered ? 'true' : 'false',
                        $buyItem->playset ? 'true' : 'false'
                    );
                    if (isset($mkmStock->inserted[0])) {
                        $mkmStock = $mkmStock->inserted[0];
                        if (isset($mkmStock->idArticle)) {
                            $mkmStock = $mkmStock->idArticle;


                            $stock->idArticleMKM = $mkmStock->idArticle;
                            $stock->modifiedMKM = substr(str_replace('T', ' ', $mkmStock->lastEdited), 0, 16);
                            $stock->price = $mkmStock->price;
                            //TODO: use $session
                            if (isset($stock->error)) {
                                $error = $stock->error;
                                $stock->remove($error);
                                $stock->save();
                                $stock->error = $error;
                            } else
                                $stock->save();
                            //end
                            if ($stock->quantity != $mkmStock->count)
                                $stock->error = $mkmStock;


                        } else
                            var_dump($mkmStock);
                    } else
                        var_dump($mkmStock);

                }
            }
        }
        if (!isset($stock->error)) {
            $buyItem->added = true;
            $buyItem->save();
        }

        return $stock;
    }

    public function add($product, $data)
    {
        $stock = $this->stockRepository->addItem($product, $data);

        if ($this->MKM)
            if ($product->idProductMKM != null) {
                if ($stock->idArticleMKM != null) {
                    $answer = $this->MKMService->increaseStock($stock->idArticleMKM, $data->quantity);
                } else {
                    $answer = $this->MKMService->addToStock($product->idProductMKM, $data->quantity, $data->price / 25, $data->state, $data->lang, "", isset($product->card) ? $product->card->foil : 0);
                    \Debugbar::info($answer);

                    if (isset($answer->inserted[0]->idArticle->idArticle))

                        $stock->idArticleMKM = $answer->inserted[0]->idArticle->idArticle;
                    $stock->save();
                }
            }
        return $stock;
    }

    public function edit($product, $data)
    {
        //prasarna opravit
        if ($product->base_price != $data['price']) {
            $product->base_price = $data['price'];
            $product->save();
        }

        if ($data['stockId'] != '') {
            $stock = $this->stockRepository->getById($data['stockId']);

            if ($data['quantity'] != 0) {
                if ($stock->price != $data['price']) {
                    $this->stockRepository->changePrice($stock, $data['price']);
                    if ($this->MKM)
                        $this->MKMService->changeArticleInStock($stock->idArticle, $stock->quantity, $data['price']);
                }
                if ($stock->state != $data['state']) {
                    $this->stockRepository->changeState($stock, $data['state']);
                    if ($this->MKM)
                        $this->MKMService->changeArticleInStock($stock->idArticle, $stock->quantity, $data['price'], $data['state']);
                }
            }

            $quantity = $stock->quantity - $data['quantity'];
            if ($quantity > 0) {

                $this->stockRepository->decreaseStock($stock, $quantity);
                if ($this->MKM)
                    $this->MKMService->decreaseStock($stock->idArticle, $quantity);
            } elseif ($quantity < 0) {
                $quantity = 0 - $quantity;
                $this->stockRepository->increaseStock($stock, $quantity);
                if ($this->MKM)
                    $this->MKMService->increaseStock($stock->idArticle, $quantity);
            }

        } else
            return $this->add($product, $data);
    }

    public function increase(Stock $stock, $quantity, $mkm = true)
    {
        $stock->quantity += $quantity;
        $stock->save();

        if ($this->MKM && $mkm) {
            $answer = null;
            if ($stock->idArticleMKM != null) {
                $answer = $this->MKMService->increaseStock($stock->idArticleMKM, $quantity);

            }
            if ($stock->idArticleMKM == null || isset($answer->error)) {

                $answer2 = $this->MKMService->addToStock($stock->all_product_id, $quantity, $stock->price);
                if ($answer2 != null) {

                    $stock->idArticleMKM = $answer2->inserted[0]->idArticle->idArticle;

                    $stock->save();
                }
            }
        }
        return $stock;
    }

    public function decrease(Stock $stock, $quantity, $mkm = true)
    {
        if ($stock->quantity < $quantity)
            $quantity = $stock->quantity;

        $stock->quantity -= $quantity;

        if ($this->MKM && $mkm) {
            if ($stock->idArticleMKM != null) {
                $answer = $this->MKMService->decreaseStock($stock->idArticleMKM, $quantity);
                \Debugbar::info($answer);
                if ($answer != null) {
                    if (isset($answer->article[0]->error))
                        $stock->idArticleMKM = null;
                }
            }
        }

        if ($stock->quantity <= 0 && count($stock->items) == 0)
            $stock->delete();
        else
            $stock->save();
        return $stock;
    }

    public function deleteMany( $items) {
        var_dump($this->MKMService->deleteManyFromStock($items));
        foreach ($items as $item) {
            $item->delete();
        }
    }
}
