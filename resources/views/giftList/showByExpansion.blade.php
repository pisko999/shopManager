@extends('layouts.app')

@if(!isset($printable))
    @php($printable = false)
@endif

@section('content')
    <div class="row">
        <div class="col-11">
            <h2>{{$expansion->name}}</h2>
        </div>
        <div class="col-1">
            <a href="{!! route('giftList.show', ['id' => $id])!!}" class="btn btn-success">zpet</a>
        </div>
    </div>
    @foreach($products as $product)
        <div class="row giftItemRow {{$product->card?->rarity->name.'Row'}}" id="row-{{$product->id}}">
            <div class="col py-1">
                <a href="http://www.mtgforfun.cz/shopping/item/{{$product->id}}"><b>{{$product->getName()}}</b></a>

            </div>
            <div class="col py-1">
                <input type="number" step="1" class="inputQuantity" value="1"/>
            </div>
            <div class="col py-1">
                <select class="selectFoil">
                    <option value="false">non-foil</option>
                    <option value="true" {{ $foil ? "selected" : ''}}>foil</option>
                </select>
            </div>
            <div class="col py-1">
                {{$foil ? $product->priceGuide->first()?->foilTrend : $product->priceGuide->first()?->trend}}
            </div>
            <div class="col py-1">

                {{$product->card?->scryfallCollectorNumber}}
            </div>
            <div class="col py-1">
                <button class="btn btn-success btn-sm giftItemAdd" data-url="{{route('giftList.addProduct',['id' => $id, 'idProduct' => $product->id])}}">add</button>
            </div>
        </div>
    @endforeach
@endsection

