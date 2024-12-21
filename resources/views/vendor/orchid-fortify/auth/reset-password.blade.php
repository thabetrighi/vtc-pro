@extends('platform::auth')
@section('title', __('app.forgot_password'))

@section('content')
    <h1 class="h4 text-black mb-4">{{ __('app.reset_password') }}</h1>

    <form role="form" method="POST" data-controller="form" data-action="form#submit"
        data-turbo="{{ var_export(Str::startsWith(request()->path(), config('platform.prefix'))) }}"
        data-form-button-animate="#button-login" data-form-button-text="{{ __('app.loading') }}"
        action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <div class="form-group">
            {!! \Orchid\Screen\Fields\Input::make('email')->type('email')->required()->tabindex(1)->autofocus()->title(__('app.email_address'))->placeholder(__('app.enter_email')) !!}
        </div>

        <div class="form-group">
            {!! \Orchid\Screen\Fields\Password::make('password')->title('Password')->autocomplete('new-password')->required()->tabindex(2)->placeholder(__('app.enter_password')) !!}
        </div>

        <div class="form-group">
            {!! \Orchid\Screen\Fields\Password::make('password_confirmation')->title('Confirm Password')->autocomplete('new-password')->required()->tabindex(3)->placeholder(__('app.enter_password')) !!}
        </div>

        <div class="row align-items-center">
            <div class="ml-auto col-md-6 col-xs-12">
                <button id="button-login" type="submit" class="btn btn-default" tabindex="4">
                    {{ __('app.reset_password') }}
                </button>
            </div>
        </div>
    </form>
@endsection
