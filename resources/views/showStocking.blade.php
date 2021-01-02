@extends("layouts.printable")



@section ('content')
    <h4>{{$expansion->name}}</h4>

    <table style="font-size: 8.5px; border: 1px black solid">
        <tr>
            @foreach($colors as $color)
                <td colspan="3"><b>{{$color}}</b></td>
            @endforeach
        </tr>
        @for($i=0;$i < $maxColorCards; $i++)
            <tr>
                @foreach($colors as $color)
                    @if(isset($col[$color][$i]))
                        <?php
                        $c = "white";
                        if ($col[$color][$i]->usd_price >= .98)
                            $c = "green";
                        if ($col[$color][$i]->usd_price >= 1.98)
                            $c = "orange";
                        switch($col[$color][$i]->rarity){
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
                        <td style="background-color: {{$d}}; border: 1px black solid; padding: 0px; margin: 0px">{{$col[$color][$i]->scryfallCollectorNumber}}</td>
                        <td style="background-color: {{$c}}; border: 1px black solid; padding: 0px; margin: 0px">{{explode('//',$col[$color][$i]->name)[0]}}</td>
                        <td style="border: 1px black solid; padding: 0px; margin: 0px; width: 16px;"></td>
                    @else
                        <td colspan="3"></td>
                    @endif
                @endforeach
            </tr>

        @endfor

        <tr></tr>
       <?php
        if ($col['multicolor']->count() > $col['artifact']->count()) {
            $a = 3;
            $b = 2;
        } else {
            $a = 2;
            $b = 3;
        }
        $m = intval($col['multicolor']->count() / $a);
        $n = intval($col['artifact']->count() / $b);
        if ($m < $n)
            $m = $n;
        ?>
        <tr>
            <td colspan="{{$a * 4}}"><b>multicolor</b></td>
            <td colspan="{{$b * 4}}"><b>artifact</b></td>
        </tr>
        @for($i=0;$i<$m;$i++)
            <tr>
                @for($j=0;$j<$a;$j++)
                    @if(isset($col['multicolor'][$i + $j * $m]))
                        <?php
                        $c = "white";
                        if ($col['multicolor'][$i + $j * $m]->usd_price >= .98)
                            $c = "green";
                        if ($col['multicolor'][$i + $j * $m]->usd_price >= 1.98)
                            $c = "orange";


                        switch ($col[$color][$i]->rarity) {
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
                        <td style="background-color: {{$d}}; border: 1px black solid; padding: 0px; margin: 0px">{{$col['multicolor'][$i + $j * $m]->scryfallCollectorNumber}}</td>
                        <td style="background-color: {{$c}}; border: 1px black solid; padding: 0px; margin: 0px">{{$col['multicolor'][$i + $j * $m]->name}}</td>
                        <td style="border: 1px black solid; padding: 0px; margin: 0px; width: 16px;"></td>
                    @else
                        <td colspan="3"></td>
                    @endif
                @endfor
                @for($k=0;$k<$b;$k++)

                    @if(isset($col['artifact'][$i + $k * $m]))

                        <?php

                        $c = "white";
                        if ($col['artifact'][$i + $k * $m]->usd_price >= .98)
                            $c = "green";
                        if ($col['artifact'][$i + $k * $m]->usd_price >= 1.98)
                            $c = "orange";

                        switch ($col[$color][$i]->rarity) {
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
                        <td style="background-color: {{$d}}; border: 1px black solid; padding: 0px; margin: 0px">{{$col['artifact'][$i + $k * $m]->scryfallCollectorNumber}}</td>
                        <td style="background-color: {{$c}}; border: 1px black solid; padding: 0px; margin: 0px">{{$col['artifact'][$i + $k * $m]->name}}</td>
                        <td style="border: 1px black solid; padding: 0px; margin: 0px; width: 16px;"></td>
                    @else
                        <td colspan="3"></td>
                    @endif
                @endfor
            </tr>

        @endfor
        <tr><td><b>Lands</b></td></tr>
        @for($i=0;$i<$max['lands'];$i++)
            <tr>
                @for($j=0;$j<5;$j++)
                    @if(isset($col['lands'][$i * 5 + $j]))
                        <?php
                        $c = "white";
                        if ($col['lands'][$i * 5 + $j]->usd_price >= .98)
                            $c = "green";
                        if ($col['lands'][$i * 5 + $j]->usd_price >= 1.98)
                            $c = "orange";


                        switch ($col['lands'][$i * 5 + $j]->rarity) {
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
                        <td style="background-color: {{$d}}; border: 1px black solid; padding: 0px; margin: 0px">{{$col['lands'][$i * 5 + $j]->scryfallCollectorNumber}}</td>
                        <td style="background-color: {{$c}}; border: 1px black solid; padding: 0px; margin: 0px">{{$col['lands'][$i * 5 + $j]->name}}</td>
                        <td style="border: 1px black solid; padding: 0px; margin: 0px; width: 16px;"></td>
                    @else
                        <td colspan="3"></td>
                    @endif
                @endfor
            </tr>
        @endfor
    </table>
@endsection


