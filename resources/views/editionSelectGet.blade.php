@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Select edition :</div>

                    <div class="card-body">
                        {{Form::open(['method'=>$m, 'route'=>$r])}}

                        {{ Form::select('id', $editions) }}
                        @if(isset($requireFoilSelect))
                            {{Form::select('foils',[0 => 'Non-Foil', 1 => 'Foil'])}}
                        @endif
                        {{Form::submit('go')}}
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
