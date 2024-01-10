<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 17/04/2019
 * Time: 19:36
 */
if (!isset($items))
    $items = array();

?>

<table width="100%">
    <thead>
    <tr>
        <th>Product name</th>
        <th>Expansion</th>
        <th>State</th>
        <th>Price p.u.</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>VAT type</th>
        @if(!Auth::guest() && Auth::user()->role >= 4 && !$printable)
            <th>Actions</th>
        @endif
    </tr>
    </thead>
    <?php $price = 0; ?>
    @foreach($items as $item)
        <?php \Debugbar::info($item->stock); ?>
        <?php $price += $item->price * $item->quantity; ?>
        <tr>
            <td>

                <a href="{!! route('shopping.show', ['id'=>$item->stock->all_product_id])  !!}" data-toggle="tooltip" title='<img src="https://www.mtgforfun.cz/storage/{{$item->stock->product->image->path}}" width="250px">'>
                    {{isset($item->stock->product)?($item->stock->product->name . ( $item->stock->isFoil == 1 ? ' - foil': '')):'token?'}}
                </a>



            </td>
            <td>{{$item->stock->product->expansion->sign}}</td>
            <td>{{$item->stock->state}}</td>
            <td>{{$item->price}}</td>
            <td>{{$item->quantity}}</td>
            <td>{{$item->price * $item->quantity}}</td>
            <td>{{$item->stock->is_new ? 'new' : 'used'}}</td>
            @if(isset($cart))
                <td>
                    {!! Form::open(['route' => 'cart.remove', 'id' => 'form' . (isset($item->id)?$item->id: '')]) !!}
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
                    <td>{{--
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
                    --}}</td>
                @endif
            @endif
        </tr>
    @endforeach
    <tr>
        <td colspan="4">
            <hr>
        </td>
    </tr>
    @if(isset($shippingMethod))
    <tr>
        <td colspan="3">
            {{$shippingMethod->method != null ? $shippingMethod->method->name:''}}
        </td>
        <td>{{$shippingMethod->price}}</td>
    </tr>
        <?php $price += $shippingMethod->price?>
    @endif
    <tr>
        <td colspan="4">
            <hr>
        </td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td>Total price:</td>
        <td>{{$price}}</td>
    </tr>
</table>
