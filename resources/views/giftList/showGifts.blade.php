@if(!isset($printable))
    @php($printable = false)
@endif

@extends($printable == true?'layouts.printable':'layouts.app')

@section('content')

       <div class="card">
           <div class="card-header">
               <div class="row">
                   <div class="col">
                       {{$giftList->id}}
                   </div>
               </div>
           </div>
           <div class="card-body">
               @foreach($gifts as $gift)
                   <h2>{{$gift->id}}</h2>
                    @foreach($gift->giftItems as $item)
                       <div class="row">
                           <div class="col">
                               {{$item->product->name . ($item->foil ? ' - foil' : '')}}
                           </div>
                           <div class="col">
                               {{$item->product->expansion->name}}
                           </div>
                           <div class="col">
                               {{$item->product->card?->scryfallCollectorNumber}}
                           </div>
                           <div class="col">
                               {{$item->product->card?->rarity->name}}
                           </div>
                           <div class="col">
                               {{$item->product->priceGuide->first()->{$item->foil ? "trend" : "foilTrend"} }}
                           </div>
                       </div>
                   @endforeach
                   <hr>
               @endforeach
           </div>
       </div>

@endsection

