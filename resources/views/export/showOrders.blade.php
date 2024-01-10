@extends('layouts.app')

@section('content')
    <table class="table-striped">
        <?php \Debugbar::info($commands); ?>
        @foreach($commands as $command)
            <tr>
                <td>{{$command->id}}</td>
                <td>{{$command->value}}</td>
            </tr>
        @endforeach
    </table>
@endsection
