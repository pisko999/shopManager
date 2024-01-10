@extends("layouts.printable")

@section ('content')
    <h4>{{$expansion->name}}
        @if($foil)
            - foil
        @endif</h4>
    <table style="font-size: 8.5px; border: 1px black solid">
        @for($i=1;$i<=$max;$i++)
            <tr>
                @for($j=0;$j<5;$j++)
                    @if(isset($cards[intval($i + $j * $max)-1]))
                        <?php
                            $card = $cards[intval($i + $j * $max)-1];
                            $price = \App\Libraries\PriceLibrary::getPrice($card->priceGuide->first()?->{$foil? 'foilTrend' : 'trend'}, \App\libraries\PriceLibrary::Eur,\App\libraries\PriceLibrary::Eur);
                            if ($price == 0.16) {
                                $price2 = \App\Libraries\PriceLibrary::getPrice($card->priceGuide->first()?->{$foil? 'foilLow' : 'lov'}, \App\libraries\PriceLibrary::Eur,\App\libraries\PriceLibrary::Eur);
                                if ($price2 > $price) {
                                    $price = $price2;
                                }
                            }
                        $c = "white";
                        if ($price >= .98)
                            $c = "green";
                        if ($price >= 1.98)
                            $c = "orange";


                        switch ($card->card->rarity?->sign) {
                            case 'M':
                                $d = 'red';
                                break;
                            case 'R':
                                $d = 'gold';
                                break;
                            case 'U':
                                $d = 'grey';
                                break;
                            default:
                                $d = 'white';
                                break;
                        }
                        if ( $i == 1 && $j < 10)
                        \Debugbar::info($card);
                        ?>
                        <td style="background-color: {{$d}}; border: 1px black solid; padding: 0px; margin: 0px">{{$card->scryfallCollectorNumber}}</td>
                            <td style="background-color: {{$c}}; border: 1px black solid; padding: 0px; margin: 0px">{{substr($card->name, 0, 32)}}</td>
                            <td> {{$price}}</td>
                        <td style="border: 1px black solid; padding: 0px; margin: 0px; width: 16px;"></td>
                        <td style="width: 10px"></td>
                    @else
                        <td colspan="4"></td>
                    @endif
                @endfor
            </tr>
        @endfor
    </table>
@endsection


