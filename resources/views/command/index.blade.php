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
                                <option
                                    value="{{$statusName->id}}" {{$commandType == $statusName->id ? "selected":''}}>{{$statusName->name}}</option>
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
                                            {{$command->id}} {{$command->shipping_method_id}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{!! route('command', ['id' => $command->id])!!}">
                                            {{$command->status->date_bought}}
                                        </a>
                                    </td>
                                    @if(!Auth::guest() && Auth::user()->role >= 4)
                                        <td>{{$command->client->mkm_username != null?$command->client->mkm_username :$command->client->name}}</td>
                                    @endif
                                    <td>
                                        <a href="{!! route('command', ['id' => $command->id])!!}">
                                            {{count($command->items)}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{!! route('command', ['id' => $command->id])!!}">
                                            {{$command->total_value}}
                                        </a>
                                    </td>
                                    <td>
                                        {{--                                        <a href="{!! route('payment.show', ['payment_id' => $command->payment_id])!!}">--}}
                                        {{$command->status->name->name}}
                                        {{--                                        </a>--}}
                                    </td>
                                    @if(!Auth::guest() && Auth::user()->role >= 4)
                                        <td>
                                            <a href="{{route('commandFacture',['id' => $command->id])}}">
                                                <button>F</button>
                                            </a>
                                            <a href="{{route('commandAddress',['id' => $command->id])}}">
                                                <button>A</button>
                                            </a>
                                            <button data-target="#ModalTrackingNumber" data-toggle="modal"
                                                    data-id="{{$command->id}}" id="btnTrackingNumber"
                                                    data-tracking_number="{{$command->tracking_number}}">TN
                                            </button>
                                            @switch($command->getStatus())
                                                @case("paid")
                                                <button class="btnSend" data-id="{{$command->id}}">Send</button>
                                                @break
                                                @case("sent") //cancellationRequested
                                                <button class="btnAcceptCancelation" data-id="{{$command->id}}">Accept
                                                    Cancelation
                                                </button>
                                                @break
                                            @endswitch

                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modals.trackingNumber')

@endsection

@section('scripts')
    @parent
    <script>
        $(document).on('click', ("#btnTrackingNumber"), function (e) {
            $('#inputId').val($(this).data('id'));
            $('#inputTrackingNumber').val($(this).data('tracking_number'));
        });
        $(document).on('submit', ("#formTrackingNumber"), function (e) {
            e.preventDefault();
            $.ajax({
                method: this.method,
                url: this.action,
                dataType: 'text',
                data: $(this).serialize(),
                success: function (answer) {
                    alert('tracking number set');
                    $('#ModalTrackingNumber').modal('toggle');
                },
                error: function (answer) {
                    console.log(answer);
                    alert('! Something went wrong !');
                }
            });
        });

        $(document).on('click', (".btnSend"), function (e) {
            url = "{{route('commandSetSend', ['id' => 1])}}".slice(0, -1) + $(this).data('id');
            $.ajax({
                type: "get",
                url: url,
                success: function (response) {
                    console.log(response);
                    alert('sent');
                },
                error: function (response) {
                    console.log(response);
                    alert('not sent');
                },
            });
        });
        $(document).on('click', (".btnAcceptCancelation"), function (e) {
            url = "{{route('commandAcceptCancellation', ['id' => 1, 'relistItems' => true])}}".slice(0, -3) +
                $(this).data('id') +
                '/' + confirm("Do you want to relist items?");


            $.ajax({
                type: "get",
                url: url,
                success: function (response) {
                    console.log(response);
                    alert('sent');
                },
                error: function (response) {
                    console.log(response);
                    alert('not sent');
                },
            });
        });
    </script>
@endsection
