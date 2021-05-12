<table>
    @foreach($items as $item)
        @if($item->stock->price > 0.16)
        <tr>
            <td>
                <a href="https://www.cardmarket.com/en/Magic/Products/Singles/{{strtr( $item->product->expansion->name, [' ' => '-', 'Core' => 'Core-Set',':' => '', '`' => ''])}}/{{strtr($item->product->name,[',' => '', '// ' => '', ' ' => '-',':' => '', '`' => ''])}}">
                    {{$item->product->name}}
                </a>
            </td>
            <td>{{$item->product->expansion->name}}</td>
            <td>{{$item->isFoil? 'foil':'non-foil'}}</td>
            <td>{{$item->stock->price}}</td>
        </tr>
        @endif
    @endforeach
</table>
