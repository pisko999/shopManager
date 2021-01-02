<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 17/04/2019
 * Time: 19:36
 */
    $items = $command->ItemsWithCardAndProduct;

?>

<table width="100%">
    <thead>
    <tr>
        <th>Item id</th>
        <th>Product name</th>
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
        <?php $price += $item->price * $item->quantity; ?>
        <tr id="trItem" data-id="{{$item->id}}">
            <td>{{$item->id}}</td>
            <td>
{{--                <a href="{!! route('shopping.show', ['itemId'=>$item->id_product])  !!}">{{$item->product->name . ($item->isFoil ? ' - foil': '')}}</a>--}}
                {{$item->product->name . ($item->isFoil ? ' - foil': '')}}
            </td>
            <td id="tdPU" data-id="{{$item->id}}">{{$item->price}}</td>
            <td id="tdQuantity" data-id="{{$item->id}}">{{$item->quantity}}</td>
            <td id="tdPT" data-id="{{$item->id}}">{{$item->price * $item->quantity}}</td>
            <td id="tdVPU" data-id="{{$item->id}}">{{$item->isFoil? $item->card->usd_price_foil:$item->card->usd_price}}</td>
            @if($command->id_status)
                <td>
                    {!! Form::open(['route' => ['buyItem.update', ['id' => $item->id]], 'id' => 'form' . (isset($item->id)?$item->id: ''), 'class' => 'formUpdateBuyItem']) !!}
                    <input name="id" value="{{$item->id}}" hidden>
                    {{Form::select('quantity',array_combine(range(1,20),range(1,20)))}}
                    <button type="submit" name="action" value="decrease">-</button>
                    <button type="submit" name="action" value="increase">+</button>
                    <button type="submit" name="action" value="remove">x</button>

                    {!! Form::close() !!}

                </td>
                @endif
        </tr>
    @endforeach
    <tr><td colspan="4"><hr></td></tr>
    <tr>
        <td colspan="2"></td>
        <td>Total price:</td>
        <td id="tdPrice">{{$price}}</td>
        <td>Total value:</td>
        <td id="tdValue">{{$command->value}}</td>

    </tr>
</table>
