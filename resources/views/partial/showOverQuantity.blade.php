<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 17/04/2019
 * Time: 19:36
 */

?>
{{Form::open(['route' => ['buyCommand.removeOverQuantity', $buyCommand->id]])}}

<table width="100%">
    <thead>
    <tr>
        <th>Item id</th>
        <th>Product name</th>
        <th>Quantity added</th>
        <th>Quantity total</th>
        @if(!Auth::guest() && Auth::user()->role >= 4 && !$printable)
            <th>Actions</th>
        @endif
    </tr>
    </thead>
    <?php $price = 0; ?>
    @foreach($items as $item)
        <tr id="trItem" data-id="{{$item->id}}">
            <td>{{$item->id}}</td>
            <td>
{{--                <a href="{!! route('shopping.show', ['itemId'=>$item->id_product])  !!}">{{$item->product->name . ($item->isFoil ? ' - foil': '')}}</a>--}}
                {{$item->product->name . ($item->isFoil ? ' - foil': '')}}
            </td>
            <td id="tdQuantity" data-id="{{$item->id}}">{{$item->quantity}}</td>
            <td id="tdQuantityOver" data-id="{{$item->id}}">{{$item->totalQuantity}}</td>
           <td>
               {{Form::checkbox('chckRemove'. $item->id , $item->totalQuantity, true, ['class'=>'chckRemove'])}}
           </td>
        </tr>
    @endforeach
    <tr><td colspan="4"><hr></td></tr>
    <tr>
        <td colspan="2"></td>
        <td>Total price:</td>
        <td id="tdPrice">{{$price}}</td>
        <td>Total value:</td>

    </tr>
</table>
{{Form::submit('remove')}}
{{Form::close()}}
{{Form::checkbox('dis','',true,['id' => 'chckButtons'])}}

@section('scripts')
    @parent
    <script>
        $(document).on('click', '#chckButtons', function (e) {
            if (this.checked) {
                $('.chckRemove').each(function () {
                    $(this).prop('checked',true);
                });
            } else {
                $('.chckRemove').each(function () {
                    $(this).prop('checked',false);
                });
            }


        });

    </script>
@endsection
