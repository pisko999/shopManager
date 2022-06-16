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
        <th>Item id</th>
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
    <?php $price = 0; ?>

    @foreach($items as $item)
        <?php
        $priceGuide = $item->product->priceGuide->first();
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
        if(is_numeric($itemPrice)) {
            $price += $itemPrice * $item->quantity;
            }
        ?>
        <tr id="trItem" data-id="{{$item->id}}">
            <td>{{$item->id}}</td>
            <td>
                {{--                <a href="{!! route('shopping.show', ['itemId'=>$item->id_product])  !!}">{{$item->product->name . ($item->isFoil ? ' - foil': '')}}</a>--}}
                <a href="https://www.cardmarket.com/en/Magic/Products/Singles/{{strtr($item->product->expansion->name, [' ' => '-', 'Core' => 'Core-Set',':' => '', '`' => ''])}}/{{strtr($item->product->name,[',' => '', '// ' => '', ' ' => '-',':' => '', '`' => '', "'" => ''])}}">
                    {{$item->product->name . ($item->isFoil ? ' - foil': '')}}
                </a>
            </td>
            <td id="tdState" data-id="{{$item->id}}">
                @if(!$printable)
                    {{Form::select('selCondition',$conditions,$item->state,['data-id' => $item->id, 'data-href' =>route('buyItem.updateState',['id'=>$item->id,'state' => $item->state]), 'class' => 'condition'])}}</td>
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
                <td width="150px">
                    {!! Form::open(['route' => ['buyItem.update', ['id' => $item->id]], 'id' => 'form' . (isset($item->id)?$item->id: ''), 'class' => 'formUpdateBuyItem']) !!}
                    <input name="id" value="{{$item->id}}" hidden>
                    {{Form::select('quantity',array_combine(range(1,20),range(1,20)))}}
                    {{Form::submit('-',['name'=>'action', 'value' => 'decrease'])}}
                    {{Form::submit('+',['name'=>'action', 'value' => 'increase'])}}
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

    </tr>
</table>
