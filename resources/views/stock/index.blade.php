@extends('layouts.app')
@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-8">Stock</div>
                            <div class="col-4"><a href="{{route('stock.edit')}}"><button class="btn btn-success">Pridat</button></a></div>
                        </div>
                    </div>

                    <div class="card-body">
                        {!! $links !!}
                        <table style="align: center; width: 100%">
                            @foreach($stock as $card)

                                <tr style="background: {{$card->background}}">
                                    <td>{{$card->scryfallCollectorNumber}}</td>
                                    <td>{{$card->product->name}}</td>
                                    <td>{{$card->price}}</td>
                                    <td>{{isset($card->state)? $card->state:''}}</td>
                                    <td>{{$card->language}}</td>
                                    <td>{{$card->isFoil?'foil':'non-foil'}}</td>
                                    <td>{{$card->signed?'signed':''}}</td>
                                    <td>{{$card->altered?'altered':''}}</td>
                                    <td>{{$card->playset?'playset':''}}</td>
                                    <td>{{$card->comments?$card->comments:''}}</td>
                                    <td class="tdQuantity" data-id="{{$card->id}}">{{$card->quantity}}</td>
                                    <td>
                                        {{Form::open(['method' => 'post', 'route' => ['stockUpdateQuantity',['id'=>$card->id]], 'class'=>'formUpdateQuantity'])}}
                                        {{Form::hidden('id',$card->id)}}
                                        {{Form::text('quantity',1,['id'=>'quantity'])}}
                                        <button type="submit" class="btnDecrease" value="decrease" data-id="{{$card->id}}">-</button>
                                        <button type="submit" class="btnIncrement" value="increase" data-id="{{$card->id}}">+</button>
                                        {{Form::close()}}
                                    </td>
                                    <td><button type="button" class="btnModify" data-id="{{$card->id}}">modify</button></td>

                                </tr>


                            @endforeach
                        </table>
                        {!! $links !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    @parent
    <script>
        $(document).on("submit", ".formUpdateQuantity", function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');
            var val = e.originalEvent.submitter.value;
            var data = form.serialize();
            var dataArray = form.serializeArray();
            data += '&action=' + val;
            var id = dataArray[1].value;

            var el = $('#trItem[data-id="' + id + '"]');
            var tv = $('#tdValue');
            var tp = $('#tdPrice');
            var pu = el.find('#tdPU');
            var pt = el.find('#tdPT');
            var quant = el.find('#tdQuantity');
            var ratioP = (tv.text() / tp.text()).toFixed(2);
            $.ajax({
                type: method,
                url: url,
                data: data, // serializes the form's elements.
                success: function (response) {
                    $('.tdQuantity[data-id="' + response.id + '"]').text(response.quantity);
                    if (response == "false")
                        alert(Error);
                    else if (val == 'remove' || (val == 'decrease' && response <= 0)) {
                        tv.text((tv.text() - (pu.text() * quant.text()* ratioP)).toFixed(2));
                        tp.text((tp.text() - (pu.text() * quant.text())).toFixed(2));
                        console.log(pt.text());
                        el.empty();
                        el.remove();

                    } else if(val == 'decrease'){
                        tv.text((tv.text() - ((quant.text() - response ) * pu.text()) * ratioP).toFixed(2));
                        tp.text((tp.text() - ((quant.text() - response ) * pu.text())).toFixed(2));
                        quant.text(response);
                        pt.text((pu.text() * response).toFixed(2));
                    }else{
                        tv.text((tv.text() + ((response  - quant.text()) * pu.text()) * ratioP).toFixed(2));
                        tp.text((tp.text() + ((response - quant.text()) * pu.text())).toFixed(2));
                        quant.text(response);
                        pt.text((pu.text() * response).toFixed(2));
                    }
                }
            });
        })
    </script>
@endsection
