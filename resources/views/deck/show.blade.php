@if(!isset($printable))
    @php($printable = false)
@endif

@extends($printable == true?'layouts.printable':'layouts.app')

@section('content')

    @if(isset($commands))
        @foreach($commands as $command)
            @include('partial.command',['command' => $command, 'printable' => $printable])
        @endforeach
    @else
        @include('partial.command',['command' => $command, 'printable' => $printable])

    @endif

@endsection

