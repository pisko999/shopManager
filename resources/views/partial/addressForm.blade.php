<div class="form-group row">
    <label for="street" class="col-md-4 col-form-label text-md-right">{{ __('Street') }}</label>

    <div class="col-md-6">
        <input id="street" type="text"
               class="form-control{{ $errors->has('street') ? ' is-invalid' : '' }}"
               value="{{ old('street') }}" name="street" required autofocus>

        @if ($errors->has('street'))
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('street') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <label for="number" class="col-md-4 col-form-label text-md-right">{{ __('Number') }}</label>

    <div class="col-md-2">
        <input id="number" type="text"
               class="form-control{{ $errors->has('number') ? ' is-invalid' : '' }}"
               value="{{ old('number') }}" name="number" required>

        @if ($errors->has('number'))
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('number') }}</strong>
                                    </span>
        @endif
    </div>
    <label for="flat" class="col-md-2 col-form-label text-md-right">{{ __('Flat') }}</label>

    <div class="col-md-2">
        <input id="flat" type="text"
               class="form-control{{ $errors->has('flat') ? ' is-invalid' : '' }}"
               value="{{ old('flat') }}" name="flat">

        @if ($errors->has('flat'))
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('flat') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <label for="city" class="col-md-4 col-form-label text-md-right">{{ __('City') }}</label>

    <div class="col-md-6">
        <input id="city" type="text"
               class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}"
               value="{{ old('city') }}" name="city" required>

        @if ($errors->has('city'))
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('city') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <label for="country" class="col-md-4 col-form-label text-md-right">{{ __('Country') }}</label>

    <div class="col-md-6">
        <input id="country" type="text"
               class="form-control{{ $errors->has('country') ? ' is-invalid' : '' }}"
               value="{{ old('country') }}" name="country" required>

        @if ($errors->has('country'))
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <label for="region" class="col-md-4 col-form-label text-md-right">{{ __('Region') }}</label>

    <div class="col-md-6">
        <input id="region" type="text"
               class="form-control{{ $errors->has('region') ? ' is-invalid' : '' }}"
               value="{{ old('region') }}" name="region">

        @if ($errors->has('region'))
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('region') }}</strong>
                                    </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <label for="postal"
           class="col-md-4 col-form-label text-md-right">{{ __('Postal code') }}</label>

    <div class="col-md-6">
        <input id="postal" type="text"
               class="form-control{{ $errors->has('postal') ? ' is-invalid' : '' }}"
               value="{{ old('postal') }}" name="postal" required>

        @if ($errors->has('postal'))
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('postal') }}</strong>
                                    </span>
        @endif
    </div>
</div>
