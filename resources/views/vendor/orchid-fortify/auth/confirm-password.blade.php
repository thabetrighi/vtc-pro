@extends('platform::auth')

@section('content')

    <h1 class="h4 text-black mb-4">{{ __('app.confirm_password') }}</h1>

    <form
        role="form"
        method="POST"
        data-controller="form"
        data-action="form#submit"
        data-turbo="{{ var_export(Str::startsWith(request()->path(), config('platform.prefix'))) }}"
        data-form-button-animate="#button-login"
        data-form-button-text="{{ __('app.loading') }}"
        action="{{ route('password.confirm') }}">
        @csrf

        <div class="form-group">

            <label class="form-label">
                {{__('app.password')}}
            </label>

            {!!  \Orchid\Screen\Fields\Password::make('password')
                ->required()
                ->autocomplete('current-password')
                ->tabindex(1)
                ->autofocus()
                ->placeholder(__('app.enter_password'))
            !!}
        </div>

        <div class="row align-items-center">
            <div class="form-group col-md-6 col-xs-12">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        {{ __('app.forgot_password') }}
                    </a>
                @endif
            </div>
            <div class="col-md-6 col-xs-12">
                <button id="button-login" type="submit" class="btn btn-default" tabindex="3">
                    {{ __('app.confirm_password') }}
                </button>
            </div>
        </div>
    </form>



@endsection
