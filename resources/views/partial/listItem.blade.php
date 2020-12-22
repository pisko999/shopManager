<?php
//return var_dump(get_class($item));

if (get_class($item) == "App\Models\AllProduct") {
    $product = $item;
    if ($item->card != null)
        $item = $item->card;
    elseif ($item->booster != null)
        $item = $item->booster;
    elseif ($item->boosterBox != null)
        $item = $item->boosterBox;
    elseif ($item->collect != null)
        $item = $item->collect;
    elseif ($item->play != null)
        $item = $item->play;
} else//if ($product instanceof \App\Models\Booster || $product instanceof \App\Models\Card || $product instanceof \App\Models\BoosterBox || $product instanceof \App\Models\Collect || $product instanceof \App\Models\Play)

    $product = $item->product;
//var_dump(($product));

$stock = $product->stock->all();

$stockItem = null;
$i = 0;
do{
if (isset($stock[$i]) && $stock[$i]->quantity == 0)
    $i++;

if (isset($stock[$i]))
    $stockItem = $stock[$i];
if (($searched->foil != -1 && $stockItem != null && $stockItem->isFoil != $searched->foil)
    || ($searched->lang != 0 && $stockItem != null && $stockItem->language != $searched->lang)) {
    $i++;
    continue;
}
$image_path =
    isset($stockItem->image) && $stockItem->image != null ?
        $stockItem->image->path :
        ($product->image != null ?
            $product->image->path :
            "");

$quantity =
    isset($stockItem->quantity) ?
        $stockItem->quantity :
        0;

$price =
    (isset($stockItem->price) && ($stockItem->quantity > 0)) ?
        $stockItem->price :
        $item->getPriceCZK();
//\Debugbar::info($price);
$foil =
    isset($stockItem) && $stockItem->isFoil == 1 ?
        'foil' :
        '';

$state = isset($stockItem->state) ? $states[$stockItem->state] : "";
?>

<div style="border: 1px solid; margin: 2px">
    <table width="100%" style="text-align: center">
        <tr>
            <td rowspan="4" class="col-md-1">
                {{\Debugbar::info($item)}}
                @if($item->CardFaces != null)
                    <div>
                        @foreach($item->images as $image)
                            <img src="{{url('/') .
                                                    "/storage/" .
                                                    $image->path
                                                }}" width="100px">
                        @endforeach
                    </div>
                @else
                    <img src="{{url('/') .
                                                    "/storage/" .
                                                    $image_path
                                                }}" width="100px">

                @endif
            </td>
            <td colspan="1" class="col-md-4" style="text-align: left">
                <a href="{!! route('shopping.show', ['itemId'=>$product->id])  !!}">
                    {{$product->name . ((isset($stockItem->language) && ($stockItem->language != 1))? (' - ' . $stockItem->Language->languageName ): '')}}
                </a>
            </td>
            <td class="col-md-3">
                quantity: {{ $quantity}}
            </td>
            <td class="col-md-3">
            {!! Form::open(['route' => 'cart.add', 'id' => 'form' . (isset($stockItem->id)?$stockItem->id: '')]) !!}
            <!--<form id="form{{isset($stockItem->id)?$stockItem->id:''}}" method="post" action="{!! route('cart.add')  !!}">-->
                <input type="text" name="price" value="{{$price}}" hidden>
                <input type="text" name="stock_id"
                       value="{{isset($stockItem->id)?$stockItem->id:''}}" hidden>
                <ul style="display: inline">
                    <li style="display: inline">
                        <select name="quantity" selectedIndex="0">
                            @for($j = 1; $j <= $quantity; $j++)
                                <option value="{{$j}}">{{$j}}</option>
                            @endfor
                        </select>
                    </li>
                    <li style="display: inline">
                        <?php $str = '$("form' . (isset($stockItem->id) ? $stockItem->id : '') . '").submit();'?>
                        <button {{$quantity < 1 ? 'disabled': ''}} onclick="{{$str}}">
                            Add to cart
                        </button>
                    </li>
                </ul>
                <!--</form>-->
                {!! Form::close() !!}
            </td>
        </tr>
        <tr>
            <td>{{$item->product != null?(
                    $item->product->expansion != null?
                        $item->product->expansion->name:''):(
                        $item->expansion != null?
                            $item->expansion->name:'')
    }}</td>
            <td>{{$item->scryfallCollectorNumber}}</td>
            <td>{{$foil}} {{$state}} {{isset($stockItem)?$stockItem->stock:''}}</td>
            <td>
                @if($item instanceof \App\Models\Card)
                    @foreach($item->colors as $color)
                        {{$color->color}}
                    @endforeach
                @endif
            </td>
        </tr>
        <tr>
            <td>{{$item->rarity !=null?$rarities[$item->rarity->sign]:''}}</td>
            <td class="col-md-2">
                <b>price: {{ $price}}</b>
            </td>

        </tr>
        @if(!Auth::guest() && Auth::user()->role >= 4)
            <tr>
                <td colspan="3">

                    {{Form::open(['method'=>'POST', 'route'=>'admin.addCardSinglePost', 'name'=>'addCardForm', 'id'=>'addCardForm'])}}
                    <input type="text" name="id" value="{{$product->id}}" hidden>
                    <label for="quantity">Quantity :</label>
                    <input type="text" id="quantity" name="quantity"
                           style="width:100px"
                           required>
                    <label for="state">State :</label>
                    <select name="state" id="state">
                        @foreach($states as $key=>$value)
                            <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                    @if(isset($lang))
                        <label for="lang">Lang :</label>
                        <select name="lang" id="lang">
                            @foreach($lang as $key=>$value)
                                <option value="{{$key}}">{{$value}}</option>
                            @endforeach
                        </select>
                    @endif
                    <label for="price">Price :</label>
                    <input type="text" name="price" id="price"
                           value="{{$price}}"
                           style="width:100px"
                           required>
                    <input type="submit" value="Add">
                    {{Form::close()}}
                    @if($quantity > 0)
                        {{Form::open(['method'=>'POST', 'route'=>'admin.removeCardSinglePost', 'name'=>'removeCardForm', 'id'=>'removeCardForm'])}}
                        <input type="text" name="id" value="{{$stockItem->id}}" hidden>
                        <label for="quantity">Quantity :</label>
                        <input type="text" id="quantity" name="quantity"
                               style="width:100px"
                               required>
                        <input type="submit" value="Remove">
                        {{Form::close()}}
                        <a href="{!! route('admin.checkCardOnMKM', ['id'=>$product->id])  !!}">check</a>

                    @endif
                </td>
            </tr>
        @endif
    </table>
</div>

<?php
$i++;
}while(isset($stock[$i]))
?>
