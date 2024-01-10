@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Gift List</div>

                    <div class="card-body">
                        {{--@if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif--}}
{{--                        {!! $links !!}--}}
                        <table id="tableDecks" style="align: center; width: 100%">
                            <thead>
                            <td>id</td>
                            <td>date</td>
                            <td>name</td>
                            <td>items</td>
                            <td>status</td>
                            @if(!Auth::guest() && Auth::user()->role >= 4)
                                <td>actions</td>
                            @endif
                            </thead>
                            @foreach($giftLists as $giftList)
                                <tr data-id="{{$giftList->id}}">
                                    <td class="col-md-2">
                                        <a href="{!! route('giftList.show', ['id' => $giftList->id])!!}">
                                            {{$giftList->id}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{!! route('giftList.show', ['id' => $giftList->id])!!}">
                                            {{$giftList->created_at}}
                                        </a>
                                    </td>
                                    <td>{{$giftList->name}}</td>
                                    <td>
                                        <a href="{!! route('giftList.show', ['id' => $giftList->id])!!}">
                                            {{$giftList->GiftItems()->count()}}{{-- TODO:count with quantity --}}
                                        </a>
                                    </td>
                                    <td>{{$giftList->status}}</td>
                                    @if(!Auth::guest() && Auth::user()->role >= 4)
                                        <td>
                                            <a href="{{route('giftList.delete',['id' => $giftList->id])}}" class="btn">X</a>

                                            <a href="{{route('giftList.showGifts',['id' => $giftList->id])}}" class="btn">
                                                Show
                                            </a>
                                        </td>
                                        {{-- <td>
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
                            {{Form::button('Add new gift list',[ 'class'=>"btn btn-info btn-sm", 'data-bs-toggle'=>"modal",
                                    'data-bs-target'=>"#ModalGiftListForm", 'id'=>"btnAddGiftList"])}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modals.giftListForm')

@endsection

@section('scripts')
    @parent
    <script>
        $(document).on('click', ("#btnAddGiftList"), function (e) {
            $('#inputId').val('');
            $('#inputName').val('');
        });
        $(document).on('submit', ("#formGiftList"), function (e) {
            e.preventDefault();
            $.ajax({
                method: this.method,
                url: this.action,
                dataType: 'text',
                data: $(this).serialize(),
                success: function (answer) {
                    toastr.success('Gift list added');
                    addGiftListToTable(answer);
                    $('#ModalGiftListForm').modal('toggle');
                },
                error: function (answer) {
                    console.log(answer);
                    toastr.error('! Something went wrong !');
                    $('#ModalGiftListForm').modal('toggle');
                }
            });
        });

        function addGiftListToTable(data){
            $("#tableDecks").append(
                '<tr data-id="' + data.id + '">' +
                '<td id="date">' +
                getValueOf(data.created_at) +
                '</td>' +
                '<td id="name">' +
                getValueOf(data.name) +
                '</td>' +
                '<td>' +
                0 +
                '</td>' +
                '<td>' +
                getValueOf(data.status) +
                '</td>' +
                '<td>' +
                "<button id='btnEditDeck' " +
                "class='btn btn-primary btn-sm' " +
                "data-toggle='modal' " +
                "data-target='#ModalEditDeck' " + // TODO: use same as add
                "data-id='" + getValueOf(data.id) + "' " +
                "data-name='" + getValueOf(data.name) + "' " +
                ">edit</button> " +
                "<button id='btnDeleteGiftList' class='btn btn-danger btn-sm' data-id='" + getValueOf(data.id) + "'>X</button>" +
                "</td>" +
                "</tr>"
            );
        }
    </script>
@endsection
