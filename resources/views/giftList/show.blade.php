@extends('layouts.app')

@if(!isset($printable))
    @php($printable = false)
@endif

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                {{Form::open(['method'=>'GET', 'route'=> ['giftList.addFromExpansion', ['id' => $giftList->id]]])}}
                    {{Form::select('idExpansion', $expansions)}}
                    {{Form::select('foils',[0 => 'Non-Foil', 1 => 'Foil'])}}
                    {{Form::submit('go')}}
                {{Form::close()}}
            </div>
        </div>
    </div>

    @include('partial.giftItems',['giftItems' => $giftList->giftItems])

@endsection

