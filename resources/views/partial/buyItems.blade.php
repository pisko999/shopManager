<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 17/04/2019
 * Time: 19:36
 */
$items = $command->ItemsWithCardAndProduct;
\Debugbar::info($items);
?>

<table width="100%">
    <thead>
    <tr>
        <th>Sold</th>
        <th>Sold Value</th>
        <th>Item id</th>
        <th>Exp.</th>
        <th>Product name</th>
        <th>Condition</th>
        <th>Price p.u.</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Value p.u.</th>
        @if(!Auth::guest() && Auth::user()->role >= 4 && !$printable)
            <th>Actions</th>
        @endif
    </tr>
    </thead>
    <?php
    $price = 0;
    $quantity = 0;
    $soldQuantity = 0;
    $soldPrice = 0;
    //$items = $items->sortBy('card.scryfallCollectorNumber')->sortBy('product.idExpansion')->sortBy('isFoil');
    ?>

    @foreach($items as $item)
        <?php
        $priceGuide = $item->product->priceGuide->first();
        try {
            $itemPrice =
                $command->getStatus() == "confirmed" ?
                    $item->stock->price
                    : (
                $priceGuide != null ?
                    \App\Libraries\PriceLibrary::getPrice(
                        $item->isFoil ?
                            ($priceGuide->foilTrend + $priceGuide->foilAvgOne + $priceGuide->foilAvgSeven) / 3 :
                            ($priceGuide->trend + $priceGuide->avgOne + $priceGuide->avgSeven) / 3,
                        \App\Libraries\PriceLibrary::Eur,
                        \App\Libraries\PriceLibrary::Eur
                    )
                    :
                    ''
                );
        } catch (Exception $exception) {
            \Debugbar::info($item);
            $itemPrice = 0;
        }
        if(is_numeric($itemPrice)) {
            $price += $itemPrice * $item->quantity;
            }
        $soldQuantityItem = 0;
        $soldPriceItem = 0;
        foreach($item->items as $buyitem) {
            \Debugbar::info($buyitem);
            $soldQuantityItem += $buyitem->pivot->quantity;
            $soldPriceItem += $buyitem->pivot->quantity * $buyitem->price;
            $soldQuantity += $soldQuantityItem;
            $soldPrice += $soldPriceItem;
        }
        ?>
        <tr id="trItem" data-id="{{$item->id}}">
            <td>{{$soldQuantityItem}}</td>
            <td>{{$soldPriceItem}}</td>
            <td id="tdId">{{$item->id}}</td>
            <td>{{$item->product->expansion?->update}} - {{$item->product->expansion?->sign}}</td>
            <td>
                {{--                <a href="{!! route('shopping.show', ['itemId'=>$item->id_product])  !!}">{{$item->product->name . ($item->isFoil ? ' - foil': '')}}</a>--}}
                <a href="https://www.cardmarket.com/en/Magic/Products/Singles/{{strtr($item->product->expansion?->name, [' ' => '-', 'Core' => 'Core-Set',':' => '', '`' => ''])}}/{{strtr($item->product->name,[',' => '', '// ' => '', ' ' => '-',':' => '', '`' => '', "'" => ''])}}" data-toggle="tooltip" title='<img src="https://www.mtgforfun.cz/storage/{{$item->product->image?->path}}" width="250px">'>
                    {{$item->product->name . ($item->isFoil ? ' - foil': '')}}
                </a>
            </td>
            <td id="tdState" data-id="{{$item->id}}">
                @if(!$printable)
                    {{Form::select('selCondition', $conditions, $item->state,['data-id' => $item->id, 'data-href' => route('buyItem.updateState',['id'=>$item->id,'state' => $item->state]), 'class' => 'condition'])}}</td>
            @else
                {{$item->state}}
            @endif
            <td id="tdPU" data-id="{{$item->id}}">
                {!! Form::open(['route' => ['buyItem.update', ['id' => $item->id]], 'id' => 'formPrice' . (isset($item->id)?$item->id: ''), 'class' => 'formUpdateBuyItem']) !!}
{{--                <input name="action" value="price" hidden>--}}
                    <input id="inputPrice" name="price" value="{{$item->price}}">
                {{Form::submit('price',['name'=>'action', 'value' => 'price'])}}

                {!! Form::close() !!}
            </td>
            <td id="tdQuantity" data-id="{{$item->id}}">{{$item->quantity}}</td>
            <td id="tdPT" data-id="{{$item->id}}">{{$item->price * $item->quantity}}</td>
            <td id="tdVPU"
                data-id="{{$item->id}}">{{$itemPrice}}</td>
            @if($command->id_status && !$printable)
                <td id="tdForm" width="150px">
                    {!! Form::open(['route' => ['buyItem.update', ['id' => $item->id]], 'id' => 'form' . (isset($item->id)?$item->id: ''), 'class' => 'formUpdateBuyItem']) !!}
                    <input name="id" value="{{$item->id}}" hidden>
                    {{Form::select('quantity',array_combine(range(1,20),range(1,20)))}}
                    {{Form::submit('-',['name'=>'action', 'value' => 'decrease'])}}
                    {{Form::submit('+',['name'=>'action', 'value' => 'increase'])}}
                    {{Form::submit('/',['name'=>'action', 'value' => 'separate'])}}
                    {{Form::submit('x',['name'=>'action', 'value' => 'remove'])}}
                    {!! Form::close() !!}

                </td>
            @endif
        </tr>
    @endforeach
    <tr>
        <td colspan="4">
            <hr>
        </td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td>Total price:</td>
        <td id="tdPrice">{{$command->initial_value}}</td>
        <td>Total value:</td>
        <td id="tdValue">{{$command->value ?: $price}}</td>
        <td>Total sold quantity:</td>
        <td id="tdPrice">{{$soldQuantity}}</td>
        <td>Total sold price:</td>
        <td id="tdPrice">{{$soldPrice}}</td>

    </tr>
</table>
