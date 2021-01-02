@extends("layouts.printable")



@section ('content')
    <h4>{{$expansion->name}}</h4>
    <table style="font-size: 8.5px; border: 1px black solid">
        @for($i=1;$i<=$max;$i++)
            <tr>
                @for($j=0;$j<5;$j++)
                    @if(isset($cards[intval($i + $j * $max)-1]))
                        <?php
                        $c = "white";
                        if ($cards[intval($i + $j * $max)-1]->usd_price >= .98)
                            $c = "green";
                        if ($cards[intval($i + $j * $max)-1]->usd_price >= 1.98)
                            $c = "orange";


                        switch ($cards[intval($i + $j * $max)-1]->rarity) {
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
                        ?>
                        <td style="background-color: {{$d}}; border: 1px black solid; padding: 0px; margin: 0px">{{$cards[intval($i + $j * $max)-1]->scryfallCollectorNumber}}</td>
                        <td style="background-color: {{$c}}; border: 1px black solid; padding: 0px; margin: 0px">{{$cards[intval($i + $j * $max)-1]->name}}</td>
                        <td style="border: 1px black solid; padding: 0px; margin: 0px; width: 16px;"></td>
                    @else
                        <td colspan="3"></td>
                    @endif
                @endfor
            </tr>
        @endfor
    </table>
@endsection


