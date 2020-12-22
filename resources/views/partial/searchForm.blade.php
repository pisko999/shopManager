<?php
//Search
//Selected
?>
<div>
    {{Form::open(['method'=>'GET', 'route'=>'shopping.searchCard', 'name'=>'searchColor', 'id'=>'searchColor'])}}
    <div>
        @if(isset($search->editions))
            {{ Form::select('edition', $search->editions, $selected->editionId) }}
        @endif
        @if(isset($search->rarities))
            {{ Form::select('rarity', $search->rarities, $selected->rarity) }}
        @endif
        @if(isset($search->foils))
            {{ Form::select('foil', $search->foils, $selected->foil) }}
        @endif

    </div>
    <div>
        @foreach($search->colors as $color)
            <input type="checkbox" value="{{$color}}" name="color[]"
                   id="color{{ucfirst($color)}}" {{ (isset($selected->colors) && in_array($color, $selected->colors)) ? "checked" : "" }}>
            <label
                for="color{{ucfirst($color)}}">{{ucfirst($color)}}{{$color=="multi"?"color":""}}</label>
        @endforeach

    </div>
    <div>
        <input type="text" id="searchText" name="searchText" autocomplete="off"
               value="{{$selected->text}}"
               style="width: 300px">
        @if(isset($search->lang))
            {{ Form::select('lang', $search->lang, $selected->lang) }}
        @endif
        <input type="checkbox" id="onlyStock"
               name="onlyStock" {{$selected->onlyStock? "checked" : ""}}>
        <label for="onlyStock">Only in stock</label>

        <input type="submit" value="Search">


        <script>
            $().ready(function () {
                    $("#searchText").focus();
                }
            )
        </script>

    </div>
    {{Form::close()}}
    {{Form::open(['method' => 'GET', 'route'=> 'shopping.search', 'name' => 'searchForm2', 'id' => 'searchForm2'])}}
    <input type="text" id="searchTextHidden" name="searchTextHidden" autocomplete="off"
           hidden>
    <div name="autoUlSearch" id="autoUlSearch"
         style="z-index: 999;position: absolute; left:0; right:0; margin-left:auto; margin-right:auto; background-color: lightgray; width: 300px">

    </div>
    {{Form::close()}}

</div>
