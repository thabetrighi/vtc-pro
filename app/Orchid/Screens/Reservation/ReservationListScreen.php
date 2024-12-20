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
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Mail;
use Orchid\Screen\Actions\Button;

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
        return '';
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
            Button::make(__('app.all'))
                ->method('filterStatus')
                ->parameters(['status' => 'all'])
                ->icon('bs.list')
                ->class('btn btn-secondary gap-1 rounded'), // Styling as a secondary button

            Button::make(__('app.pending'))
                ->method('filterStatus')
                ->parameters(['status' => 'pending'])
                ->icon('bs.hourglass-split')
                ->class('btn btn-warning gap-1 rounded'),

            Button::make(__('app.ongoing'))
                ->method('filterStatus')
                ->parameters(['status' => 'ongoing'])
                ->icon('bs.arrow-right-circle')
                ->class('btn btn-info gap-1 rounded'),

            Button::make(__('app.completed'))
                ->method('filterStatus')
                ->parameters(['status' => 'completed'])
                ->icon('bs.check-circle')
                ->class('btn btn-success gap-1 rounded'),

            Button::make(__('app.canceled'))
                ->method('filterStatus')
                ->parameters(['status' => 'canceled'])
                ->icon('bs.x-circle')
                ->class('btn btn-danger gap-1 rounded'),

            Button::make(__('app.past'))
                ->method('filterStatus')
                ->parameters(['status' => 'past'])
                ->icon('bs.clock-history')
                ->class('btn btn-dark gap-1 rounded'),
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

        Toast::info(__('app.reservation_saved_success'));
    }

    public function remove(Request $request): void
    {
        Reservation::findOrFail($request->get('id'))->delete();

        Toast::info(__('app.reservation_removed_success'));
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
        // if (!$reservation->user?->email) {
        //     Toast::error(__('app.no_valid_email'));
        //     return back();
        // }

        try {
            // Define email language options
            $invoiceLanguages = settings('invoice_languages', ['en', 'fr']) ?? ['en', 'fr'];

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

            Toast::info(__('app.invoice_email_sent'));
        } catch (\Exception $e) {
            logDebug('email', [$e]);
            Toast::error(__('app.invoice_email_failed'));
        }

        return back();
    }

    /**
     * Cancel the specified reservation.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelReservation(int $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'canceled') {
            $reservation->update(['status' => 'canceled']);
            Toast::info(__('app.reservation_canceled'));
        } else {
            Toast::warning(__('app.reservation_already_canceled'));
        }

        return redirect()->route('platform.reservations');
    }

    /**
     * Filter reservations based on the status.
     *
     * @param string $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function filterStatus(string $status)
    {
        if ($status === 'all') {
            return redirect()->route('platform.reservations');
        }

        return redirect()->route('platform.reservations', ['filter[status]' => $status]);
    }
}
