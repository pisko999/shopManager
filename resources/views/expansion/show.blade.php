@extends('layouts.app')

@section('content')
    <table class="table-striped">
        <tr>
            <td>{{$expansion->id}}</td>
            <td>{{$expansion->name}}</td>
            <td>{{$soldPrice}}</td>
            <td>{{$expansion->added}}</td>
            <td><a href="{{route('expansions.changeUpdate', ['id' => $expansion->id])}}">{{$expansion->update}}</a></td>
        </tr>
    </table>
        @foreach($expansion->AllCardsWithStock as $product)
            @foreach($product->stock as $stock)
                @if($stock->quantity > 0)
                    <div class="row">
                        <div class="col-1">{{$product->name}}</div>
                        <div class="col-3">{{$product->name}}</div>
                        <div class="col-3">{{$stock->comment}}</div>
                        <div class="col-1">{{$stock->isFoil}}</div>
                        <div class="col-1">{{$stock->quantity}}</div>
                        <div class="col-1">{{$stock->price}}</div>
                    </div>
                @endif
            @endforeach
        @endforeach

@endsection
