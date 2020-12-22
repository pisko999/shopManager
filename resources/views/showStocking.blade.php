@extends("layouts.printable")



@section ('content')
    <h4>{{$list[$colors[0]][0]->expansion->name}}</h4>

    <table style="font-size: 8.5px; border: 1px black solid">
        <tr>
            @foreach($colors as $color)
                <td colspan="3"><b>{{$color}}</b></td>
            @endforeach
        </tr>
        @for($i=0;$i < $max; $i++)
            <tr>
                @foreach($colors as $color)
{{\Debugbar::info($list[$color][$i])}}
                    @if(isset($list[$color][$i]))
                        <?php
                        $c = "white";
                        if ($list[$color][$i]->usd_price >= .98)
                            $c = "green";
                        if ($list[$color][$i]->usd_price >= 1.98)
                            $c = "orange";
                        switch($list[$color][$i]->rarity){
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
                        <td style="background-color: {{$d}}; border: 1px black solid; padding: 0px; margin: 0px">{{$list[$color][$i]->scryfallCollectorNumber}}</td>
                        <td style="background-color: {{$c}}; border: 1px black solid; padding: 0px; margin: 0px">{{explode('//',$list[$color][$i]->name)[0]}}</td>
                        <td style="border: 1px black solid; padding: 0px; margin: 0px; width: 16px;"></td>
                    @else
                        <td colspan="3"></td>
                    @endif
                @endforeach
            </tr>

        @endfor

        <tr></tr>
       <?php
        if ($list['Multicolor']->count() > $list['Colorless']->count()) {
            $a = 3;
            $b = 2;
        } else {
            $a = 2;
            $b = 3;
        }
        $m = intval($list['Multicolor']->count() / $a);
        $n = intval($list['Colorless']->count() / $b);
        if ($m < $n)
            $m = $n;
        ?>
        <tr>
            <td colspan="{{$a * 4}}"><b>Multicolor</b></td>
            <td colspan="{{$b * 4}}"><b>Colorless</b></td>
        </tr>
        @for($i=0;$i<$m;$i++)
            <tr>
                @for($j=0;$j<$a;$j++)
                    @if(isset($list['Multicolor'][$i + $j * $m]))
                        <?php
                        $c = "white";
                        if ($list['Multicolor'][$i + $j * $m]->usd_price >= .98)
                            $c = "green";
                        if ($list['Multicolor'][$i + $j * $m]->usd_price >= 1.98)
                            $c = "orange";


                        switch ($list[$color][$i]->rarity) {
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
                        <td style="background-color: {{$d}}; border: 1px black solid; padding: 0px; margin: 0px">{{$list['Multicolor'][$i + $j * $m]->scryfallCollectorNumber}}</td>
                        <td style="background-color: {{$c}}; border: 1px black solid; padding: 0px; margin: 0px">{{$list['Multicolor'][$i + $j * $m]->name}}</td>
                        <td style="border: 1px black solid; padding: 0px; margin: 0px; width: 16px;"></td>
                    @else
                        <td colspan="3"></td>
                    @endif
                @endfor
                @for($k=0;$k<$b;$k++)

                    @if(isset($list['Colorless'][$i + $k * $m]))

                        <?php

                        $c = "white";
                        if ($list['Colorless'][$i + $k * $m]->usd_price >= .98)
                            $c = "green";
                        if ($list['Colorless'][$i + $k * $m]->usd_price >= 1.98)
                            $c = "orange";

                        switch ($list[$color][$i]->rarity) {
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
                        <td style="background-color: {{$d}}; border: 1px black solid; padding: 0px; margin: 0px">{{$list['Colorless'][$i + $k * $m]->scryfallCollectorNumber}}</td>
                        <td style="background-color: {{$c}}; border: 1px black solid; padding: 0px; margin: 0px">{{$list['Colorless'][$i + $k * $m]->name}}</td>
                        <td style="border: 1px black solid; padding: 0px; margin: 0px; width: 16px;"></td>
                    @else
                        <td colspan="3"></td>
                    @endif
                @endfor
            </tr>
        @endfor
    </table>
@endsection


