@extends('layouts.app')
@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Adding cards from {{$expansion->name}}</div>

                    <div class="card-body">
                        {!! $links !!}
                        {{Form::checkbox('dis','',false,['id' => 'chckButtons'])}}
                        {{Form::label('dis','disable buttons')}}
                        {{Form::open(['method' => 'post', 'url' => url($cards->url($cards->currentPage()))])}}
                        <table style="align: center; width: 100%">
                            @foreach($cards as $card)
                                <tr style="background: {{$card->background}}">
                                    <td>{{$card->scryfallCollectorNumber}}</td>
                                    <td>{{$card->name}}</td>
                                    <td>{{$card->quantity}}</td>
                                    <td>
{{--                                        {{Form::text('quantity_po_'. $card->id,0,['id'=>'quantity_po_'.$card->id, 'style' => 'width:20px;background-color:#dc3545'])}}--}}
{{--                                        {{Form::text('quantity_pl_'. $card->id,0,['id'=>'quantity_pl_'.$card->id, 'style' => 'width:20px;background-color:#e56773'])}}--}}
{{--                                        {{Form::text('quantity_lp_'. $card->id,0,['id'=>'quantity_lp_'.$card->id, 'style' => 'width:20px;background-color:#fd8a2b'])}}--}}
{{--                                        {{Form::text('quantity_gd_'. $card->id,0,['id'=>'quantity_gd_'.$card->id, 'style' => 'width:20px;background-color:#ffc107'])}}--}}
{{--                                        {{Form::text('quantity_ex_'. $card->id,0,['id'=>'quantity_ex_'.$card->id, 'style' => 'width:20px;background-color:#82891e'])}}--}}
                                        {{Form::text('quantity_nm_'. $card->id,0,['id'=>'quantity_nm_'.$card->id, 'style' => 'width:20px;background-color:#3daf57'])}}
{{--                                        {{Form::text('quantity_mt_'. $card->id,0,['id'=>'quantity_mt_'.$card->id, 'style' => 'width:20px;background-color:#17a2b8'])}}--}}
                                    </td>
                                    <td>
                                        <button type="button" class="btnDecrease" data-id="{{$card->id}}">-</button>
                                    </td>
                                    <td>
                                        <button type="button" class="btnIncrement" data-id="{{$card->id}}">+</button>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        {{Form::submit('Save & Next Page')}}
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).on('click', '#chckButtons', function (e) {
            if (this.checked) {
                $('.btnIncrement').each(function () {
                    $(this).attr('disabled', 'disabled');
                });
                $('.btnDecrease').each(function () {
                    $(this).attr('disabled', 'disabled');
                });
            } else {
                $('.btnIncrement').each(function () {
                    $(this).removeAttr('disabled');
                });
                $('.btnDecrease').each(function () {
                    $(this).removeAttr('disabled');
                });
            }


        });
        $(document).ready(function() {
            $('#chckButtons').focus();
        });

        $(document).on('click', '.btnDecrease', function (e) {
            var id = $(this).data('id');
            $('#quantity' + id).val(parseInt($('#quantity' + id).val()) - 1);
        });

        $(document).on('click', '.btnIncrement', function (e) {
            var id = $(this).data('id');
            $('#quantity' + id).val(parseInt($('#quantity' + id).val()) + 1);
        });
    </script>
@endsection
