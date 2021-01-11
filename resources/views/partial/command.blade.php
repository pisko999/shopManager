@if( $printable == true)
    <page size="A4">
        @else
            <div class="container">
                @endif
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">

                            <div class="card-body">
                                <table width="100%">
                                    <tr>
                                        <td style="border: black 1px solid">@include('partial.address')</td>
                                        <td style="border: black 1px solid">Order : {{$command->id}}</td>
                                    </tr>
                                    <tr>
                                        <td style="border: black 1px solid">@if($command->payment != null)@include('partial.payment',['payment' => $command->payment])@endif</td>
                                        <td style="border: black 1px solid">@include('partial.address',['address' => $command->billing_address, 'user' => $command->client])</td>
                                    </tr>
                                    <tr>
                                        <td><br/><br/></td>
                                    </tr>
                                    <tr style="border: black 1px solid">
                                        <td colspan="2">@include('partial.items',['items' => $command->items, 'printable' => $printable, 'shippingMethod' => $command->shippingMethod])</td>
                                    </tr>


                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @if(!(isset($printable) && $printable == true))
            </div>
            @else
    </page>
@endif
