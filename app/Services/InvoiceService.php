<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\PDF;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;

class InvoiceService
{
    /**
     * Generate invoice PDF for the reservation.
     *
     * @param Reservation $reservation
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePDF(Reservation $reservation)
    {
        return PDF::loadView('invoice', [
            'reservation' => $reservation,
            'generated_at' => now(),
        ]);
    }

    /**
     * Download invoice PDF for the reservation.
     *
     * @param Reservation $reservation
     * @return StreamedResponse
     */
    public function download(Reservation $reservation): StreamedResponse
    {
        $pdf = $this->generatePDF($reservation);
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'invoice_' . $reservation->id . '_' . now()->format('Y-m-d_H:i:s') . '.pdf'
        );
    }

    /**
     * View invoice PDF in browser.
     *
     * @param Reservation $reservation
     * @return Response
     */
    public function view(Reservation $reservation): Response
    {
        $pdf = $this->generatePDF($reservation);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice.pdf"');
    }
} 