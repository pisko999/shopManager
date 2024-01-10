@if(!isset($printable))
    @php($printable = false)
@endif

@extends($printable == true?'layouts.printable':'layouts.app')

@section('content')

    @foreach($commands as $command)
       <div class="card">
           <div class="card-header">
               <div class="row">
                   <div class="col">
                       {{$command->id}}
                   </div>
                   <div class="col">
                       {{$command->id}}
                   </div>
                   <div class="col">
                       {{$command->client->mkm_username != null?$command->client->mkm_username : $command->client->name}}
                   </div>
                   <div class="col">
                       {{$command->billing_address?->name}}
                   </div>
               </div>
           </div>
           <div class="card-body">
               @foreach($command->gifts as $gift)
                   <h2>{{$gift->name}}</h2>
                    @foreach($gift->giftItems as $item)
                       <div class="row">
                           <div class="col">
                               {{$item->product->name . ($item->foil ? ' - foil' : '')}}
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
               @endforeach
           </div>
       </div>
    @endforeach

@endsection

