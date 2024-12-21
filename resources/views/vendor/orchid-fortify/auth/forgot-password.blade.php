@extends('platform::auth')
@section('title', __('app.forgot_password'))

@section('content')
    <h1 class="h4 text-black mb-4">{{__('app.forgot_password')}}</h1>

    <form
        role="form"
        method="POST"
        data-controller="form"
        data-action="form#submit"
        data-turbo="{{ var_export(Str::startsWith(request()->path(), config('platform.prefix'))) }}"
        data-form-button-animate="#button-login"
        data-form-button-text="{{ __('app.loading') }}"
        action="{{ route('password.email') }}">
        @csrf

        @if (session('status'))
            <div class="alert alert-info rounded shadow-sm" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <p>
        {{ __('app.forgot_password_detail') }}
        </p>

        <div class="form-group">
            {!!
                \Orchid\Screen\Fields\Input::make('email')
                ->type('email')
                ->autofocus()
                ->autocomplete('email')
                ->placeholder('Enter your email')
                ->title('E-Mail Address')
            !!}
        </div>

        <div class="row align-items-center">
            <div class="ml-auto col-md-8 col-xs-12">
                <button id="button-login" type="submit" class="btn btn-default" tabindex="2">
                    {{ __('app.email_password_reset_link') }}
                </button>
            </div>
        </div>
    </form>
@endsection
