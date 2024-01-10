@extends('layouts.app')

@section('content')
    {{Form::open(['method' => 'post'])}}
    <button type="submit">zobrazit</button>
        @foreach($expansions as $type => $list)
            <div class="card">
                <div class="card-header">
                    <input type="checkbox" id="expansionType{{$type}}" class="expansion-type" data-type="{{$type}}"/>
                    <label for="expansionType{{$type}}">{{$type}}</label>
                </div>
                <div class="row">
                    @foreach($list as $item)
                        <div class="col">
                            <input type="checkbox" id="expansion{{$type}}{{$item->sign}}" class="expansion-{{$type}}" value="{{$item->id}}" name="ids[]"/>
                            <label for="expansion{{$type}}{{$item->sign}}">{{$item->name}}</label>
                        </div>
                    @endforeach

                </div>
            </div>
        @endforeach
    {{Form::close()}}
        <?php \Debugbar::info($expansions); ?>

@endsection

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.expansion-type').forEach(
                (el) => {
                    el.addEventListener('click',
                        (e) => {
                            let type = e.target.dataset.type;
                            document.querySelectorAll('.expansion-' + type).forEach(
                                (e) => {
                                    e.checked = el.checked;
                                }
                            )
                        })
                })
        });
    </script>
@endsection
