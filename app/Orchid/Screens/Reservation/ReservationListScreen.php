<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Reservation;

use App\Mail\InvoiceMail;
use App\Models\Reservation;
use App\Orchid\Layouts\Reservation\ReservationEditLayout;
use App\Orchid\Layouts\Reservation\ReservationFiltersLayout;
use App\Orchid\Layouts\Reservation\ReservationListLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Mail;

class ReservationListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'reservations' => Reservation::with('user')
                ->filters(ReservationFiltersLayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Reservation Management';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'A comprehensive list of all registered reservations, including their profiles and privileges.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.reservations.list',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->route('platform.reservations.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            ReservationFiltersLayout::class,
            ReservationListLayout::class,

            Layout::modal('editReservationModal', ReservationEditLayout::class)
                ->deferred('loadReservationOnOpenModal'),
        ];
    }

    /**
     * Loads reservation data when opening the modal window.
     *
     * @return array
     */
    public function loadReservationOnOpenModal(Reservation $reservation): iterable
    {
        return [
            'reservation' => $reservation,
        ];
    }

    public function saveReservation(Request $request, Reservation $reservation): void
    {
        $request->validate([
            'reservation.email' => [
                'required',
                Rule::unique(Reservation::class, 'email')->ignore($reservation),
            ],
        ]);

        $reservation->fill($request->input('reservation'))->save();

        Toast::info(__('Reservation was saved.'));
    }

    public function remove(Request $request): void
    {
        Reservation::findOrFail($request->get('id'))->delete();

        Toast::info(__('Reservation was removed'));
    }

    /**
     * Generate invoice PDF for the reservation.
     *
     * @param Reservation $reservation
     * @return \Barryvdh\DomPDF\PDF
     */
    private function generateInvoicePDF(Reservation $reservation)
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
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadInvoice(Reservation $reservation)
    {
        $pdf = $this->generateInvoicePDF($reservation);
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'invoice_' . $reservation->id . '_' . now()->format('Y-m-d_H:i:s') . '.pdf'
        );
    }

    /**
     * View invoice PDF in browser.
     *
     * @param Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function viewInvoice(Reservation $reservation)
    {
        $pdf = $this->generateInvoicePDF($reservation);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice.pdf"');
    }

    /**
     * Send the invoice email with PDF attachment.
     *
     * @param Reservation $reservation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendInvoiceEmail(Reservation $reservation)
    {
        // Validate email existence
        if (!$reservation->user?->email) {
            Toast::error(__('No valid email address found for this reservation.'));
            return back();
        }

        try {
            // Define email language options
            $invoiceLanguages = settings('invoice_languages', ['en']); // Default to 'en' if no setting

            // Send invoice to reservation user
            if ($reservation->user && $reservation->user->email) {
                foreach ($invoiceLanguages as $lang) {
                    Mail::to($reservation->user->email)->send(new InvoiceMail($reservation, $lang));
                }
            }

            // Send invoice to passenger, if applicable
            if ($reservation->passenger_email) {
                foreach ($invoiceLanguages as $lang) {
                    Mail::to($reservation->passenger_email)->send(new InvoiceMail($reservation, $lang));
                }
            }

            Toast::info(__('Invoice email sent successfully.'));
        } catch (\Exception $e) {
            Toast::error(__('Failed to send invoice email. Please try again later.'));
        }

        return back();
    }
}
