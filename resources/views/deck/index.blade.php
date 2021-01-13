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
                        {!! $links !!}
                        <table id="tableDecks" style="align: center; width: 100%">
                            <thead>
                            <td>id</td>
                            <td>date</td>
                            @if(!Auth::guest() && Auth::user()->role >= 4)
                                <td>client</td>
                            @endif
                            <td>items</td>
                            <td>status</td>
                            @if(!Auth::guest() && Auth::user()->role >= 4)
                                <td>actions</td>
                            @endif
                            </thead>
                            @foreach($decks as $deck)
                                <tr data-id="{{$deck->id}}">
                                    <td class="col-md-2">
                                        <a href="{!! route('deck.show', ['id' => $deck->id])!!}">
                                            {{$deck->id}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{!! route('deck.show', ['id' => $deck->id])!!}">
                                            {{$deck->created_at}}
                                        </a>
                                    </td>
                                    @if(!Auth::guest() && Auth::user()->role >= 4)
                                        <td>{{$deck->user->name}}</td>
                                    @endif
                                    <td>
                                        <a href="{!! route('deck.show', ['id' => $deck->id])!!}">
                                            {{$deck->Cards()->count()}}{{-- TODO:count with quantity --}}
                                        </a>
                                    </td>
                                    @if(!Auth::guest() && Auth::user()->role >= 4)
                                        {{-- <td>
                                             <a href="{{route('commandFacture',['id' => $deck->id])}}">
                                                 <button>F</button>
                                             </a>
                                             <a href="{{route('commandAddress',['id' => $deck->id])}}">
                                                 <button>A</button>
                                             </a>
                                             <button data-target="#ModalTrackingNumber" data-toggle="modal"
                                                     data-id="{{$deck->id}}" id="btnTrackingNumber"
                                                     data-tracking_number="{{$deck->tracking_number}}">TN
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

                                         </td>--}}
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                        <div class="row">
                            {{Form::button('Add new deck',[ 'class'=>"btn btn-info btn-sm", 'data-toggle'=>"modal",
                                    'data-target'=>"#ModalDeckForm", 'id'=>"btnAddDeck"])}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modals.deckForm')

@endsection

@section('scripts')
    @parent
    <script>
        $(document).on('click', ("#btnAddDeck"), function (e) {
            $('#inputId').val('');
            $('#inputUserId').val({{Auth::user()->id}});
            $('#inputName').val('');
        });
        $(document).on('submit', ("#formDeck"), function (e) {
            e.preventDefault();
            $.ajax({
                method: this.method,
                url: this.action,
                dataType: 'text',
                data: $(this).serialize(),
                success: function (answer) {
                    addDeckToTable(answer);
                    alert('Deck added');
                    $('#ModalDeckForm').modal('toggle');
                },
                error: function (answer) {
                    console.log(answer);
                    alert('! Something went wrong !');
                    $('#ModalDeckForm').modal('toggle');
                }
            });
        });

        function addDeckToTable(data){
            $("#tableDecks").append(
                '<tr data-id="' + data.id + '">' +
                '<td id="name">' +
                getValueOf(data.name) +
                '</td>' +
                '<td id="date">' +
                getValueOf(data.created_at) +
                '</td>' +
                '<td>' +
                0 +
                '</td>' +
                '<td>' +
                0 +
                '</td>' +
                '<td>' +
                "<button id='btnEditDeck' " +
                "class='btn btn-primary btn-sm' " +
                "data-toggle='modal' " +
                "data-target='#ModalEditDeck' " + // TODO: use same as add
                "data-id='" + getValueOf(data.id) + "' " +
                "data-name='" + getValueOf(data.name) + "' " +
                ">edit</button> " +
                "<button id='btnDeleteDeck' class='btn btn-danger btn-sm' data-id='" + getValueOf(data.id) + "'>X</button>" +
                "</td>" +
                "</tr>"
            );
        }

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
