<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/base.js?t=' . time()) }}" defer></script>
    <script src="{{ asset('js/searchInput.js?t=' . time()) }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/js/toastr.js"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>

@yield('scripts')

<!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/css/toastr.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css?t=' . time()) }}" rel="stylesheet">
    <link href="{{ asset('css/my.css?t=' . time()) }}" rel="stylesheet">
    <link href="{{ asset('css/searchInput.css?t=' . time()) }}" rel="stylesheet">
    @yield('css')
</head>
<body>

@if(session()->has('messages'))
    <?php
    foreach (session('messages') as $message){
    ?>
    <div style="background-color: {{$message['type'] == "success"? "lightgreen" : "red"}}">{{$message['message']}}</div>

    <?php }
    $messages = session()->pull('messages', array());
    ?>
@endif
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <a class="navbar-brand" href="{{ url('/') }}">
                Sandbox: {{ env('APP_SANDBOX') ? 'true':'false' }}
            </a>
            <a class="navbar-brand" href="{{ url('/') }}">
                Database: {{ env('DB_HOST') . '/' . env('DB_DATABASE') }}
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

{{--                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">--}}
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
{{--                            </div>--}}
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">

        <div align="center" class="row">
            <div class="col-12 col-sm-2">
                <ul class="navbar-nav ">
                    <li class="nav-item">
                        <a href="{!! route('getMKMStock')!!}">
                            <div>
                                get mkm stock
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('getMKMStockFile')!!}">
                            <div>
                                get stockFile
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('setStockFromFile')!!}">
                            <div>
                                save stock from file
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('commands')!!}">
                            <div>
                                Commands
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('buyCommand.index')!!}">
                            <div>
                                Re-buys
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('buyCommand.actual')!!}">
                            <div>
                                actual re-buy
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('buyCommandEditionSelect')!!}">
                            <div>
                                Re-buy by edition
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('stockEditSelect')!!}">
                            <div>
                                stock edit
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('stockingList')!!}">
                            <div>
                                stocking
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('test')!!}">
                            <div>
                                test
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('testPdf')!!}">
                            <div>
                                test PDF
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#">
                            <div id="setPdfAddressesPosition" data-href="{!! route('commandAddresses.setPosition',['position' => 0])!!}">
                                set PDF addresses position
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('commandAddresses')!!}">
                            <div id="PdfAddresses">
                            PDF addresses
                        </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('commandPrintPaid')!!}">
                            <div>
                                PDF factures
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('deck.index')!!}">
                            <div>
                                Decks
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('expansions')!!}">
                            <div>
                                Expansions
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('expansion.card')!!}" target="_blank">
                            <div>
                                Expansion Cards
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <button data-target="#ModalCheckOrderMKM" data-toggle="modal"
                                id="btnCheckOrderMKM">
                            Check MKM order
                        </button>
                    </li>
                    <hr/>
                    <li class="nav-item">
                        <a href="{!! route('giftList.index')!!}">
                            <div>
                                Gift Lists
                            </div>
                        </a>
                    </li>
                    <hr/>
                    <li class="nav-item">
                        <a href="{!! route('stock.index')!!}">
                            <div>
                                Stock
                            </div>
                        </a>
                    </li>
                </ul>

            </div>

            <div class="col-12 col-sm-10">
                @yield('content')
            </div>
        </div>
    </main>
</div>
@include('modals.checkOrderMKM')

</body>
<script>
    $(document).on('click', '#setPdfAddressesPosition', function (e) {
        position = prompt('Starting position?');
        url = $(this).data('href').slice(0,-1) + position;
        $.ajax({
            'method': "Get",
            'url': url,

            success: function (response) {

                alert('position set to : ' + response);

            },        })

    })
</script>

</html>
