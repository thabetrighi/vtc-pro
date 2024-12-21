@extends('platform::auth')
@section('title', __('app.two_factor_auth'))

@section('content')
    <h1 class="h4 text-black mb-4">
        <x-orchid-icon path="screen-smartphone" class="h4 mr-1" />

        {{ __('app.Two-Factor Authentication') }}
    </h1>

    <form role="form" method="POST" data-controller="form" data-action="form#submit"
        data-turbo="{{ var_export(Str::startsWith(request()->path(), config('platform.prefix'))) }}"
        data-form-button-animate="#button-login" data-form-button-text="{{ __('app.loading') }}"
        action="{{ route('two-factor.login') }}">
        @csrf

        <div class="form-group">
            <p>
                {{ __('app.confirm_two_factor_access') }}
            </p>
            {!! \Orchid\Screen\Fields\Input::make('code')->autofocus()->inputmode('numeric')->pattern('[0-9]*')->autocomplete('one-time-code')->placeholder('Verification code from application')->title('Authentication code:') !!}
        </div>

        <div class="form-group">
            {!! \Orchid\Screen\Fields\Input::make('recovery_code')->autofocus()->placeholder(__('app.confirm_two_factor_access_emergency_recovery'))->help(__('app.confirm_two_factor_access_recovery'))->title(__('app.confirm_two_factor_access_recovery_label')) !!}
        </div>

        <div class="row align-items-center">
            <div class="ml-auto col-md-6 col-xs-12">
                <button id="button-login" type="submit" class="btn btn-default btn-block" tabindex="2">
                    {{ __('app.login') }}
                </button>
            </div>
        </div>
    </form>

@endsection
