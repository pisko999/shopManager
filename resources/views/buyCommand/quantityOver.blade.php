@extends(isset($printable) && $printable == true?'layouts.printable':'layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">


                <div class="card-body">
                    <table width="100%">
                        <tr>
                            <td style="border: black 1px solid"></td>
                            <td style="border: black 1px solid">Order : {{$buyCommand->id}}</td>
                        </tr>
                        <tr>
                            <td><br/><br/></td>
                        </tr>
                        @if(!isset($printable))
                            <?php $printable = false;?>
                        @endif
                        <tr style="border: black 1px solid">
                            <td colspan="2">@include('partial.showOverQuantity',['items' => $items, 'buyCommand' => $buyCommand])</td>
                        </tr>
                    </table>
                    {{Form::open(['route' => ['buyCommandMake', 'id' => $buyCommand->id]])}}
                    {{Form::text('value', $buyCommand->initial_value)}}
                    {{Form::submit('Submit')}}
                    {{Form::close()}}
                    <a href="{{route('buyCommandClose', $buyCommand->id)}}"><button class="btn">Close</button></a>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('scripts')
    @parent
    <script>
        $(document).on("submit", ".formUpdateBuyItem", function (e) {
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
