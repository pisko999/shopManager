<?php
//sortedEditionsTypes
//editions
//route
//
?>
@foreach($editions as $type => $expansions)
    <div class="card">
        <div class="card-header">{{$type}}</div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <ul class="row">
                @foreach($expansions as $key => $expansion)
                    {{\Debugbar::info($expansion)}}
                    <div class="col-md-4">

                        <a href="{!! route($route, ['edition_id'=>$key])  !!}">
                            <div>
                                {{$expansion}}
                            </div>
                        </a>
                    </div>
                @endforeach
            </ul>
        </div>
    </div>
@endforeach
