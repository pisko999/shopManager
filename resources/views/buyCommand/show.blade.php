@extends(isset($printable) && $printable == true?'layouts.printable':'layouts.app')

@section('content')
    <div class="container">
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
                                <td colspan="2">@include('partial.buyItems',['command' => $buyCommand, 'printable' => $printable])</td>
                            </tr>
                        </table>
                        {{Form::open(['route' => ['buyCommandMake', 'id' => $buyCommand->id]])}}
                        {{Form::text('value')}}
                        {{Form::submit('Submit')}}
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
            console.log(data);
            $.ajax({
                type: method,
                url: url,
                data: data, // serializes the form's elements.
                success: function (response) {
                    if (response == "false")
                        alert(Error);
                    else if (val == 'remove' || (val == 'decrease' && response <= 0)) {
                        var el = $('#trItem[data-id="' + id + '"]');
                        el.empty();
                        el.remove();
                    } else {
                        var el = $('#tdQuantity[data-id="' + id + '"]')
                        el.text(response);
                    }
                }
            });
        })
    </script>
@endsection
