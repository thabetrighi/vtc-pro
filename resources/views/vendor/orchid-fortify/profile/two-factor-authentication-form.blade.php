@if (\Laravel\Fortify\Features::canManageTwoFactorAuthentication())

    @if (session('status') == 'two-factor-authentication-enabled')
        {{-- Show SVG QR Code, After Enabling 2FA --}}
        <div class="px-4 py-2">
            {{ __('app.two_factor_enabled') }}
        </div>

        <div class="text-center p-3">
            {!! auth()->user()->twoFactorQrCodeSvg() !!}
        </div>
    @endif

    @if (auth()->user()->two_factor_recovery_codes)
        {{-- Show 2FA Recovery Codes --}}
        <div class="px-4 py-2">
            {{ __('app.store_recovery_codes') }}
        </div>

        <div class="bg-light px-4 py-2">
            @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                <p class="m-0 text-black">{{ $code }}</p>
            @endforeach
        </div>
    @endif
@endif
