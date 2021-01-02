@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Choice what you want</div>

                    <div class="card-body">
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
                            @foreach($buyCommands as $command)
                                <tr>
                                    <td class="col-md-2">
                                        <a href="{!! route('buyCommand.show', ['id' => $command->id])!!}">
                                            {{$command->id}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{!! route('buyCommand.show', ['id' => $command->id])!!}">
                                            {{$command->created_at}}
                                        </a>
                                    </td>
                                    @if(!Auth::guest() && Auth::user()->role >= 4)
                                        <td>{{$command->client->name}}</td>
                                    @endif
                                    <td>
                                        <a href="{!! route('buyCommand.show', ['id' => $command->id])!!}">
                                            {{$command->items != null?$command->items->count():0}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{!! route('buyCommand.show', ['id' => $command->id])!!}">
                                            {{$command->value()}}
                                        </a>
                                    </td>
                                    <td>
                                        {{--                                        <a href="{!! route('payment.show', ['payment_id' => $command->payment_id])!!}">--}}
                                                                                    {{$command->status->status->name}}<!--$command->status->name->name-->
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
