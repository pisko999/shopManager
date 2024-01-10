@extends('layouts.app')

@section('content')
    @foreach($games as $id => $name)
        <a href="{{route('expansions', ['idGame' => $id])}}"><button>{{$name}}</button></a>
    @endforeach
    <hr>
    @if(isset($idGame))
        @foreach($expansions->keys() as $typename)
            <a href="{{route('expansions', ['idGame' => $idGame, 'type' => $typename])}}"><button>{{$typename}}</button></a>
        @endforeach
    @endif
    @if(isset($type))
        <table class="table-striped">
            @foreach($expansions->get($type) as $expansion)
                <tr>
                    <td>{{$expansion->id}}</td>
                    <td><a href="{{route('expansions.show', ['id' => $expansion->id])}}">{{$expansion->name}}</a></td>
                    <td>{{$expansion->added}}</td>
                    <td><a href="{{route('expansions.changeUpdate', ['id' => $expansion->id])}}">{{$expansion->update}}</a></td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection
