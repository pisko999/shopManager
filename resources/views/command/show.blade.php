@if(!isset($printable))
    @php($printable = false)
@endif

@extends($printable == true?'layouts.printable':'layouts.app')

@section('content')

    @if(isset($commands))
        @foreach($commands as $command)
            @include('partial.command',['command' => $command, 'printable' => $printable])
        @endforeach
    @elseif(!isset($command) || !($command instanceof(\App\Models\Command::class)))
        <div>command not found</div>
    @else
        @include('partial.command',['command' => $command, 'printable' => $printable])

    @endif

@endsection

