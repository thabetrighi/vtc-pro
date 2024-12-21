@extends('platform::auth')

@section('content')

    <h1 class="h4 text-black mb-4">{{ __('app.verify_email') }}</h1>

    <form
        role="form"
        method="POST"
        data-controller="form"
        data-turbo="{{ var_export(Str::startsWith(request()->path(), config('platform.prefix'))) }}"
        data-action="form#submit"
        data-form-button-animate="#button-login"
        data-form-button-text="{{ __('app.loading') }}"
        action="{{ route('verification.send') }}">
        @csrf

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-info rounded shadow-sm" role="alert">
                {{ __('app.verification_link_sent') }}
            </div>
        @endif

        <p>
        {{ __('app.verify_email_message') }}
        </p>

        <div class="row align-items-center">
            <div class="ml-auto col-md-8 col-xs-12">
                <button id="button-login" type="submit" class="btn btn-default">
                    {{ __('app.resend_verification_email') }}
                </button>
            </div>
        </div>
@endsection
