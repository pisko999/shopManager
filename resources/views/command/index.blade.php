@extends('layouts.app')

@section('content')
    <div class="">
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
                        <div class="row mb-2">
                            <div class="col-4">
                                <div class="btn btn-success actions" data-action="facture">facture</div>
                                <div class="btn btn-success actions" data-action="address">address</div>
                                <div class="btn btn-success actions mr-auto" data-action="addGift">Add gift</div>
                                <div class="btn btn-success actions mr-auto" data-action="showGifts">Show gifts</div>
                            </div>
                            <div class="col-4 ">
                                {{Form::open(['method'=>'get', 'class' => 'form-inline'])}}
                                    <label class="mx-2" for="commandType">Type: </label>
                                    <select class="form-control mx-2" id="commandType" name="commandType">
                                        <option value="all" selected>All</option>
                                        @foreach($statusNames as $statusName)
                                            <option
                                                value="{{$statusName->id}}" {{$commandType == $statusName->id ? "selected":''}}>{{$statusName->name}}</option>
                                        @endforeach
                                    </select>
                                    <label class="mx-2" for="presale">Presale:</label><input type="checkbox" name="presale" @if ($presale) checked="checked" @endif>
                                    <button class="btn btn-primary mx-2" type="submit">Submit</button>
                                {{Form::close()}}
                            </div>
                            <div class="offset-3 col-1">
                                <div class="btn btn-success actions" data-action="send">send</div>
                            </div>
                        </div>
                        {!! $links !!}
                        {{Form::open(['method'=>'get', 'route' => 'command.action', 'id' => 'formCommands'])}}
                        {{Form::hidden('action','',['id' => 'action'])}}

                        <table class="table table-striped table-hover table-sm" style="align: center; width: 100%">
                            <thead>
                            <td>#</td>
                            <td><input type="checkbox" id="chbSelectAll" /></td>
                            <td>id</td>
                            <td>date</td>
                            @if(!Auth::guest() && Auth::user()->role >= 4)
                                <td>client</td>
                                <td>name</td>
                            @endif
                            <td>items</td>
                            <td>gifts</td>
                            <td>total</td>
                            <td>status</td>
                            <td>shipping</td>
                            @if(!Auth::guest() && Auth::user()->role >= 4)
                                <td>actions</td>
                            @endif
                            </thead>
                            <tbody >
                            @foreach($commands as $command)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="checkbox" class="chbCommandId" name="commandIds[]" value="{{$command->id}}" /></td>
                                    <td>
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
                                        <td>{{$command->client->mkm_username != null?$command->client->mkm_username : $command->client->name}}</td>
                                        <td>{{$command->billing_address?->name}}</td>
                                    @endif
                                    <td>
                                        <a href="{!! route('command', ['id' => $command->id])!!}">
                                            {{count($command->items)}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{!! route('command', ['id' => $command->id])!!}">
                                            {{count($command->gifts)}}
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
                                    </td>
                                    <td>
                                        {{$command->shippingMethod?->method->name}}{{--                                        </a>--}}
                                    </td>
                                    @if(!Auth::guest() && Auth::user()->role >= 4)
                                        <td>
                                            <div class="row d-table">
                                                <div class="col-3 d-table-cell"><a
                                                        href="{{route('commandFacture',['id' => $command->id])}}">
                                                        <button class="btn btn-primary">F</button>
                                                    </a>
                                                </div>
                                                <div class="col-3 d-table-cell">
                                                    <a href="{{route('commandAddress',['id' => $command->id])}}">
                                                        <button class="btn btn-secondary">A</button>
                                                    </a>
                                                </div>
                                                <div class="col-3 d-table-cell">
                                                    @if($command->shippingMethod != null && $command->shippingMethod->is_insured)
                                                        <button class="btn btn-info" data-target="#ModalTrackingNumber" data-toggle="modal"
                                                                data-id="{{$command->id}}" id="btnTrackingNumber"
                                                                data-tracking_number="{{$command->tracking_number}}">TN
                                                        </button>
                                                    @endif
                                                </div>
                                                <div class="col-3 d-table-cell">
                                                    @switch($command->getStatus())
                                                        @case("bought")
                                                        <button class="btnPaid btn btn-outline-success" data-id="{{$command->id}}">Payed</button>
                                                        @break
                                                        @case("paid")
                                                        <button class="btnSend btn btn-outline-success" data-id="{{$command->id}}">Send</button>
                                                        @break
                                                        @case("sent") <!--cancellationRequested-->
                                                        <button class="btnAcceptCancelation btn btn-outline-success" data-id="{{$command->id}}">
                                                            Accept
                                                            Cancelation
                                                        </button>
                                                        @break
                                                    @endswitch
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            {!! $links !!}
                            </tbody>
                        </table>
                        {{Form::close()}}
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
        $(document).on('click', (".btnPaid"), function (e) {
            url = "{{route('commandSetPaid', ['id' => 1])}}".slice(0, -1) + $(this).data('id');
            $.ajax({
                type: "get",
                url: url,
                success: function (response) {
                    console.log(response);
                    alert('paid');
                },
                error: function (response) {
                    console.log(response);
                    alert('not paid');
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.4.1/jquery.jscroll.min.js"></script>
    <script type="text/javascript">
        $('ul.pagination').hide();
        $(function() {
            $('.scrolling-pagination').jscroll({
                autoTrigger: true,
                padding: 0,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.scrolling-pagination',
                callback: function() {
                    $('ul.pagination').remove();
                }
            });
        });
    </script>
@endsection
