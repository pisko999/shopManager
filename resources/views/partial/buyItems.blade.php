<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 17/04/2019
 * Time: 19:36
 */
    $items = $command->items;

?>

<table width="100%">
    <thead>
    <tr>
        <th>Product name</th>
        <th>Price p.u.</th>
        <th>Quantity</th>
        <th>Price</th>
        @if(!Auth::guest() && Auth::user()->role >= 4 && !$printable)
            <th>Actions</th>
        @endif
    </tr>
    </thead>
    <?php $price = 0; ?>
    @foreach($items as $item)
        <?php $price += $item->price * $item->quantity; ?>
        <tr id="trItem" data-id="{{$item->id}}">
            <td>
{{--                <a href="{!! route('shopping.show', ['itemId'=>$item->id_product])  !!}">{{$item->product->name . ($item->isFoil ? ' - foil': '')}}</a>--}}
                {{$item->product->name . ($item->isFoil ? ' - foil': '')}}
            </td>
            <td>{{$item->price}}</td>
            <td id="tdQuantity" data-id="{{$item->id}}">{{$item->quantity}}</td>
            <td>{{$item->price * $item->quantity}}</td>
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
        <td>{{$price}}</td>

    </tr>
</table>
