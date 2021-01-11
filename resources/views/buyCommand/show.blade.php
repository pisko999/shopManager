@extends(isset($printable) && $printable == true?'layouts.printable':'layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">


                <div class="card-body">
                    <table width="100%">
                        <tr>
                            <td style="border: black 1px solid">@if($buyCommand->payment != null)@include('partial.payment',['payment' => $command->payment])@endif</td>
                            <td style="border: black 1px solid">Order : {{$buyCommand->id}}</td>
                        </tr>
                        <tr>
                            <td><br/><br/></td>
                        </tr>
                        @if(!isset($printable))
                            <?php $printable = false;?>
                        @endif
                        <tr style="border: black 1px solid">
                            <td colspan="2">@include('partial.buyItems',['command' => $buyCommand, 'printable' => $printable, 'conditions' => $conditions])</td>
                        </tr>
                    </table>
                    {{Form::open(['route' => ['buyCommand.make', 'id' => $buyCommand->id]])}}
                    {{Form::text('value', $buyCommand->initial_value)}}
                    {{Form::submit('Submit')}}
                    {{Form::close()}}
                    <a href="{{route('buyCommand.checkQuantity', $buyCommand->id)}}">
                        <button class="btn">Check quantity</button>
                    </a>
                    <a href="{{route('buyCommand.close', $buyCommand->id)}}">
                        <button class="btn">Close</button>
                    </a>
                    <a href="{{route('buyCommand.showStocking', $buyCommand->id)}}">
                        <button class="btn">Stocking</button>
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('scripts')
    @parent
    <script>
        $(document).on("change", ".condition", function (e) {
            var element = $(this).val();
            var url = $(this).data('href').slice(0, -2) + $(this).val();
            console.log(element);
            $.ajax({
                type: "post",
                url: url,
                data:{
                    _token: "{{csrf_token()}}",
                },
                success: function (answer) {
                    alert("changed");
                },
                error: function (answer) {
                    alert(answer);
                }
            })
        });
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
                        tv.text((tv.text() - (pu.text() * quant.text() * ratioP)).toFixed(2));
                        tp.text((tp.text() - (pu.text() * quant.text())).toFixed(2));
                        console.log(pt.text());
                        el.empty();
                        el.remove();

                    } else if (val == 'decrease') {
                        tv.text((tv.text() - ((quant.text() - response) * pu.text()) * ratioP).toFixed(2));
                        tp.text((tp.text() - ((quant.text() - response) * pu.text())).toFixed(2));
                        quant.text(response);
                        pt.text((pu.text() * response).toFixed(2));
                    } else {
                        tv.text((tv.text() + ((response - quant.text()) * pu.text()) * ratioP).toFixed(2));
                        tp.text((tp.text() + ((response - quant.text()) * pu.text())).toFixed(2));
                        quant.text(response);
                        pt.text((pu.text() * response).toFixed(2));
                    }
                }
            });
        })
    </script>
@endsection
