<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $reservation->id }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.3;
            color: #000;
            margin: 50px 50px 10px 50px;
            padding: 15px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 3px;
            vertical-align: top;
        }

        .header {
            border-bottom: 1px solid #000;
            margin-bottom: 15px;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }

        .company-name {
            font-size: 20px;
        }

        .company-info {
            font-size: 14px;
            margin-bottom: 50px;
        }

        .passenger-info {
            border: 1px solid #000;
            padding: 5px;
            margin-bottom: 15px;
        }

        .receipt-number {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .details-table td {
            border: 1px solid #000;
            font-size: 14px;
        }

        .details-table td:first-child {
            font-weight: bold;
            width: 30%;
        }

        .price-section {
            margin: 15px 0;
            text-align: right;
        }

        .price-box {
            display: inline-block;
            border: 1px solid #000;
            padding: 3px 8px;
            margin-left: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .total-box {
            border: 1px solid #000;
            padding: 8px;
            margin-top: 15px;
        }

        .total-box td:last-child {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
        }

        .approval-box {
            margin-bottom: 15px;
        }

        .signature-line {
            width: 200px;
            border-bottom: 1px solid #000;
            margin: 10px auto;
        }

        .stamp-box {
            display: inline-block;
            padding: 10px;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>

<body>
    <table class="header">
        <tr>
            <td width="50%">
                <strong class="company-name">{{ settings('site_name', 'Company name') }}</strong><br>
                <span class="company-info">
                    {{ settings('street_name', 'Number + street name') }}<br>
                    {{ settings('zip_code', 'zip code') }} {{ settings('city', 'City') }}<br>
                    Email: {{ settings('company_email', 'contact@company.com') }}<br>
                    Phone: {{ settings('company_phone', '+1234567890') }}<br>
                    SIRET: {{ settings('siret_number', '12345678900000') }}<br>
                    VAT: {{ settings('tva_number', 'FR12345678900') }}
                </span>
            </td>
            <td width="50%" class="logo">
                <img src="{{ getStorageFile(settings('company_logo')) }}" width="100" height="100" />
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td width="60%">
                <div class="receipt-number">
                    Receipt/Note N°: {{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}/{{ date('Y') }}
                </div>
            </td>
            <td width="40%">
                <div class="passenger-info">
                    <strong>City and Date:</strong> {{ 'Paris' }}
                    {{ $reservation->created_at->format('d/m/Y') }}<br>
                    <strong>Passenger:</strong> {{ $reservation->passenger_name }}<br>
                    <strong>Phone:</strong> {{ $reservation->passenger_phone }}<br>
                    <strong>Email:</strong> {{ $reservation->passenger_email }}
                </div>
            </td>
        </tr>
    </table>

    <table class="details-table">
        <tr>
            <td>Date and time:</td>
            <td>{{ $reservation->departure_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Driver:</td>
            <td>{{ $reservation->user->name ?? 'Not assigned' }}</td>
        </tr>
        <tr>
            <td>Vehicle:</td>
            <td>[Brand, model, color, plate number]</td>
        </tr>
        <tr>
            <td>Mode:</td>
            <td>{{ ucfirst($reservation->mode) }}</td>
        </tr>
        <tr>
            <td>Start Address:</td>
            <td>
                {{ $reservation->pickup_street }}<br>
                {{ $reservation->pickup_zip_code }} {{ $reservation->pickup_city }}<br>
                Note: {{ $reservation->pickup_note ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td>Destination Address:</td>
            <td>
                {{ $reservation->destination_street }}<br>
                {{ $reservation->destination_zip_code }} {{ $reservation->destination_city }}<br>
                Note: {{ $reservation->destination_note ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td>Passenger count:</td>
            <td>{{ $reservation->passenger_count }}</td>
        </tr>
        <tr>
            <td>Additional info:</td>
            <td>{{ $reservation->additional_info ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Alternate phone:</td>
            <td>{{ $reservation->alt_phone ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="price-section">
        <span class="price-box">Price/U:
            €{{ number_format($reservation->fare / $reservation->passenger_count, 2) }}</span>
        <span class="price-box">Total: €{{ number_format($reservation->fare, 2) }}</span>
    </div>

    <table>
        <tr>
            <td>
                <strong>Payment Details</strong><br>
                Payment method: {{ ucfirst($reservation->payment_method) }}
            </td>
        </tr>
    </table>

    <table class="total-box">
        <tr>
            <td>Total amount ex VAT</td>
            <td>€{{ number_format($reservation->fare / 1.1, 2) }}</td>
        </tr>
        <tr>
            <td>VAT 10%</td>
            <td>€{{ number_format($reservation->fare - $reservation->fare / 1.1, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td><strong>€{{ number_format($reservation->fare, 2) }}</strong></td>
        </tr>
    </table>

    <div class="footer">
        <div class="approval-box">
            <p>Read and approved<br>Good for agreement</p>
            <div class="signature-line"></div>
            Passenger Signature
        </div>
        <div class="stamp-box">
            <img src="{{ getStorageFile(settings('company_stamp')) }}" width="80" height="80" />
        </div>
    </div>
</body>

</html>
