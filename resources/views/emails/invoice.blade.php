<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Invoice Email') }}</title>
</head>

<body>
    <h1>{{ __('email.greeting', ['name' => $reservation->passenger_name]) }}</h1>
    <p>{{ __('email.thanks') }}</p>
    <p><strong>{{ __('email.reservation_id') }}</strong> {{ $reservation->id }}</p>
    <p><strong>{{ __('email.pickup_location') }}</strong> {{ $reservation->pickup_location }}</p>
    <p><strong>{{ __('email.destination_location') }}</strong> {{ $reservation->destination_location }}</p>
    <p><strong>{{ __('email.fare') }}</strong> ${{ $reservation->fare }}</p>
    <p>{{ __('email.appreciation') }}</p>
</body>

</html>
