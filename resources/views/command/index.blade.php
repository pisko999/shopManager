@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Choice what you want</div>

                    <div class="card-body">
                        {{--@if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif--}}
                        {{Form::open(['method'=>'get'])}}
                        <label for="commandType">Type: </label>
                        <select id="commandType" name="commandType">
                            <option value="all" selected>All</option>
                            @foreach($statusNames as $statusName)
                                <option value="{{$statusName->id}}" {{$commandType == $statusName->id ? "selected":''}}>{{$statusName->name}}</option>
                            @endforeach
                        </select>
                        <button type="submit">Submit</button>
                        {{Form::close()}}
                        {!! $links !!}
                        <table style="align: center; width: 100%">
                            <thead>
                            <td>id</td>
                            <td>date</td>
                            @if(!Auth::guest() && Auth::user()->role >= 4)
                                <td>client</td>
                            @endif
                            <td>items</td>
                            <td>total</td>
                            <td>status</td>
                            @if(!Auth::guest() && Auth::user()->role >= 4)
                                <td>actions</td>
                            @endif
                            </thead>
                            @foreach($commands as $command)
                                <tr>
                                    <td class="col-md-2">
                                        <a href="{!! route('command', ['id' => $command->id])!!}">
                                            {{$command->id}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{!! route('command', ['id' => $command->id])!!}">
                                            {{$command->status->date_bought}}
                                        </a>
                                    </td>
                                    @if(!Auth::guest() && Auth::user()->role >= 4)
                                        <td>{{$command->client->name}}</td>
                                    @endif
                                    <td>
                                        <a href="{!! route('command', ['id' => $command->id])!!}">
                                            {{count($command->items)}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{!! route('command', ['id' => $command->id])!!}">
                                            {{$command->amount()}}
                                        </a>
                                    </td>
                                    <td>
                                        {{--                                        <a href="{!! route('payment.show', ['payment_id' => $command->payment_id])!!}">--}}
                                                                                    {{$command->status->name->name}}
                                        {{--                                        </a>--}}
                                    </td>
                                    @if(!Auth::guest() && Auth::user()->role >= 4)
                                        <td>actions</td>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
