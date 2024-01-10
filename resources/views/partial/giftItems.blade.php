<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 17/04/2019
 * Time: 19:36
 */
?>

<table width="100%">
    <thead>
    <tr>
        <th>Product name</th>
        <th>Expansion</th>
        <th>Quantity</th>
        <th>Quantity rest</th>
        <th>Value</th>
        @if(!Auth::guest() && Auth::user()->role >= 4 && !$printable)
            <th>Actions</th>
        @endif
    </tr>
    </thead>
    <?php $price = 0; ?>
    @foreach($giftItems as $item)
            <?php $price += (
        $item->foil ?
            $item->product?->priceGuide->first()?->foilTrend :
            $item->product?->priceGuide->first()?->trend
        ) * $item->quantity_rest; ?>
        <tr>
            <td>

                <a href="{!! route('shopping.show', ['id'=>$item->all_product_id])  !!}">
                    {{isset($item->product) ? ($item->product->name . ( $item->foil == 1 ? ' - foil': '')) : 'token?'}}
                </a>

            </td>
            <td>{{$item->product?->expansion->sign}}</td>
            <td>{{$item->quantity}}</td>
            <td>{{$item->quantity_rest}}</td>
            <td>{{
    $item->foil ?
        $item->product?->priceGuide->first()?->foilTrend :
        $item->product?->priceGuide->first()?->trend
        }}
                {{\Debugbar::info($item->product?->priceGuide->first())}}
            </td>
            @if(isset($cart))
                <td>
                    {!! Form::open(['route' => 'cart.remove', 'id' => 'form' . (isset($item->id) ? $item->id : '')]) !!}
                    <input name="id" value="{{$item->id}}" hidden>
                    <select name="quantity" selectedIndex="0">
                        @for($i = 1; $i <= $item->quantity; $i++)
                            <option value="{{$i}}">{{$i}}</option>
                        @endfor
                    </select>
                    <button type="submit">remove</button>

                    {!! Form::close() !!}

                </td>
            @else

                @if(!Auth::guest() && Auth::user()->role >= 4 && !$printable)
                    <td>
                        <a href="#" data-url="{{route('giftList.deleteItem',['id' => $item->id])}}" class="btn btn-danger deleteGiftItem">X</a>
                    </td>
                    {{--<td>
                        {!! Form::open(['route' => 'command.removeItem', 'id' => 'form' . (isset($item->id)?$item->id : '')]) !!}
                        <input name="id" value="{{$item->id}}" hidden>
                        <select name="quantity" selectedIndex="0">
                            @for($i = 1; $i <= $item->quantity; $i++)
                                <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                        <input type="submit" name="remove" value="remove"/>

                        {!! Form::close() !!}
                        @if($item->stock->quantity > 0)
                            {!! Form::open(['route' => 'command.addItem', 'id' => 'form' . (isset($item->id)?$item->id: '')]) !!}
                            <input name="id" value="{{$item->id}}" hidden>
                            <select name="quantity" selectedIndex="0">
                                @for($i = 1; $i <= $item->stock->quantity; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor

                            </select>
                            <input type="submit" name="add" value="add"/>

                            {!! Form::close() !!}
                        @endif
                        </td>
                    --}}
                @endif
            @endif
        </tr>
    @endforeach
</table>
<div>
    Total: {{$price}}
</div>

