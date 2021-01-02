<table>
    @foreach($items as $item)
        @if($item->stock->price > 0.16)
        <tr>
            <td>{{$item->product->name}}</td>
            <td>{{$item->product->expansion->name}}</td>
            <td>{{$item->isFoil? 'foil':'non-foil'}}</td>
            <td>{{$item->stock->price}}</td>
        </tr>
        @endif
    @endforeach
</table>
