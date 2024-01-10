@extends("layouts.printable")



@section ('content')
    <?php $i = 0; ?>
    <div class="page" size="A4">
        <div class="row">

            @foreach($expansions as $expansion)
                <?php
                    $i++;
                    if ($i > 12) {
                        $i = 1;
                ?>
        </div>
    </div>
    <div class="page" size="A4">
        <div class="row">
            <?php
                }
            ?>
            <div class="col">
                <div class="expansion-card">
                    <h1 style="border-bottom: 1px dashed;">
                        <img class="expansion-icon" alt="" src="https://www.mtgforfun.cz/storage/public/expansionIcons/{{ $expansion->sign }}.SVG" onerror="this.style.display = 'none'">
                        {{ $expansion->name }}
                    </h1>
                    <hr class="card-hr"/>
                    <div style="text-align: left;padding-left: 0.2cm">
                        <p>Sign: {{$expansion->sign}}</p>
                        <p>Released: {{$expansion->release_date}}</p>
                        <p>Type: {{$expansion->type}}</p>
                    </div>
                </div>
            </div>
            @endforeach
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        TOKEN--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        TOKEN--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        TOKEN--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        TOKEN--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        LAND--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        LAND--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        LAND--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        LAND--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        FOIL--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        FOIL--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        FOIL--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="expansion-card">--}}
{{--                    <h1 style="border-bottom: 1px dashed;">--}}
{{--                        FOIL--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
    </div>

@endsection


