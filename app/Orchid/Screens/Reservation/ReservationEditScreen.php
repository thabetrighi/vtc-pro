<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Reservation;

use App\Mail\InvoiceMail;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ReservationEditScreen extends Screen
{
    /**
     * @var Reservation
     */
    public $reservation;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @param Reservation $reservation
     * @return array
     */
    public function query(Reservation $reservation): iterable
    {
        $reservation->load('user');

        return [
            'reservation' => $reservation,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return $this->reservation->exists ? 'Edit Reservation' : 'Reservation';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return '';
    }

    /**
     * Permission required to access this screen.
     */
    public function permission(): ?iterable
    {
        return [
            'platform.reservations.create',
            'platform.reservations.edit',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->confirm(__('Once the reservation is deleted, all of its data will be permanently deleted.'))
                ->method('remove')
                ->canSee($this->reservation->exists),

            // Button::make(__('Save'))
            //     ->icon('bs.check-circle')
            //     ->method('save'),
        ];
    }

    /**
     * Define the layout for this screen.
     *
     * @return array
     */
    public function layout(): iterable
    {
        return [

            Layout::rows([
                Group::make([
                    Select::make('reservation.mode')
                        ->title(__('Mode'))
                        ->options([
                            'transfer' => 'Transfer',
                            'ride' => 'Ride',
                        ])
                        ->help('Choose between a transfer or ride')
                        ->required(),

                    DateTimer::make('reservation.departure_at')
                        ->title(__('Departure Date and Time'))
                        ->help('Scheduled pickup date and time')
                        ->required()
                        ->format24hr()
                        ->enableTime(),
                ]),
            ])->title('Mode'),

            Layout::columns([
                Layout::rows([
                    Input::make('reservation.pickup_location')
                        ->title(__('Number and Street'))
                        // ->help('Main pickup location or landmark')
                        ->required()
                        ->placeholder(__('Enter Number and Street')),

                    Group::make([
                        Input::make('reservation.pickup_zip_code')
                            ->title(__('ZIP Code'))
                            // ->help('ZIP/Postal code of pickup location')
                            ->nullable()
                            ->placeholder(__('Enter pickup ZIP code')),

                        Input::make('reservation.pickup_city')
                            ->title(__('City'))
                            // ->help('City of pickup location')
                            ->nullable()
                            ->placeholder(__('Enter pickup city')),
                    ]),

                    TextArea::make('reservation.pickup_note')
                        ->title(__('Note'))
                        // ->help('Additional instructions for pickup location')
                        ->rows(3)
                        ->placeholder(__('Enter pickup note')),

                ])->title('Pickup Locations'),

                Layout::rows([
                    Input::make('reservation.destination_location')
                        ->title(__('Number and Street'))
                        // ->help('Main destination location or landmark')
                        ->required()
                        ->placeholder(__('Enter Number and Street')),

                    Group::make([
                        Input::make('reservation.destination_zip_code')
                            ->title(__('ZIP Code'))
                            // ->help('ZIP/Postal code of destination')
                            ->nullable()
                            ->placeholder(__('Enter destination ZIP code')),

                        Input::make('reservation.destination_city')
                            ->title(__('City'))
                            // ->help('City of destination')
                            ->nullable()
                            ->placeholder(__('Enter destination city')),
                    ]),

                    TextArea::make('reservation.destination_note')
                        ->title(__('Note'))
                        // ->help('Additional instructions for destination')
                        ->rows(3)
                        ->placeholder(__('Enter destination note')),

                ])->title('Destination Locations'),
            ]),

            Layout::rows([
                Group::make([
                    Input::make('reservation.passenger_name')
                        ->title(__('Full Name'))
                        ->help('First and Last name of the passenger')
                        ->required()
                        ->placeholder(__('Enter First and Last name')),

                    Input::make('reservation.passenger_email')
                        ->title(__('Passenger Email'))
                        ->help('Contact email for booking confirmation')
                        ->type('email')
                    // ->placeholder(__('Enter passenger email')),
                ]),
                Group::make([
                    Input::make('reservation.passenger_phone')
                        ->title(__('Passenger Phone'))
                        // ->help('Primary contact number')
                        ->type('tel')
                        ->placeholder(__('Enter passenger phone')),

                    Input::make('reservation.passenger_count')
                        ->title(__('Passengers Count'))
                        ->help('Total number of passengers')
                        ->type('number')
                        ->min(1)
                        ->required()
                        ->placeholder(__('Enter passenger count')),
                ]),
            ])->title('Passenger Info'),

            Layout::rows([
                // Departure and Payment
                Group::make([
                    Select::make('reservation.payment_method')
                        ->title(__('Payment Method'))
                        // ->help('Select payment method for the ride')
                        ->options([
                            'cash' => 'Cash',
                            'card' => 'Card',
                        ])
                        ->required(),

                    Input::make('reservation.fare')
                        ->title(__('Fare'))
                        // ->help('Total cost of the ride')
                        ->type('number')
                        ->step(0.01)
                        ->required()
                        ->placeholder(__('Enter fare')),
                ]),

                // Additional Info
                Group::make([
                    TextArea::make('reservation.additional_info')
                        ->title(__('Additional Information'))
                        ->help('Any other relevant details about the reservation')
                        ->rows(3)
                    // ->placeholder(__('Enter additional information')),
                ]),

                // Timestamps
                Group::make([
                    Input::make('reservation.created_at')
                        ->title(__('Created At'))
                        ->readonly()
                        ->canSee($this->reservation->exists),

                    Input::make('reservation.updated_at')
                        ->title(__('Updated At'))
                        ->readonly()
                        ->canSee($this->reservation->exists),
                ]),
            ]),

            Layout::rows([
                Group::make([
                    Button::make($this->reservation->exists ? __('Save') : __('Reservation'))
                        ->icon('bs.check-circle')
                        ->class('btn btn-black btn-lg btn-link icon-link p-2 px-4 rounded-3')
                        ->method('save'),
                ])->set('class', 'd-flex justify-content-center'),
            ])
        ];
    }

    /**
     * Save reservation details.
     *
     * @param Reservation $reservation
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Reservation $reservation, Request $request)
    {
        $request->validate([
            'reservation.mode'                 => ['required', 'in:transfer,ride'],
            'reservation.departure_at'         => ['required', 'date', 'after_or_equal:day'],
            'reservation.pickup_location'      => ['required_if:mode,transfer', 'string', 'max:255'],
            'reservation.pickup_street'        => ['nullable', 'string', 'max:255'],
            'reservation.pickup_zip_code'      => ['nullable', 'string', 'max:255'],
            'reservation.pickup_city'          => ['nullable', 'string', 'max:255'],
            'reservation.pickup_note'          => ['nullable', 'string', 'max:255'],
            'reservation.destination_location' => ['required_if:mode,transfer', 'string', 'max:255'],
            'reservation.destination_street'   => ['nullable', 'string', 'max:255'],
            'reservation.destination_zip_code' => ['nullable', 'string', 'max:255'],
            'reservation.destination_city'     => ['nullable', 'string', 'max:255'],
            'reservation.destination_note'     => ['nullable', 'string', 'max:255'],
            'reservation.passenger_name'       => ['required', 'string', 'max:255'],
            'reservation.passenger_email'      => ['required', 'string', 'email', 'max:255'],
            'reservation.passenger_phone'      => ['required', 'string', 'max:255'],
            'reservation.passenger_count'      => ['required', 'integer', 'min:1'],
            'reservation.additional_info'      => ['nullable', 'string'],
            'reservation.alt_phone'            => ['nullable', 'string', 'max:255'],
            'reservation.payment_method'       => ['required', 'in:cash,card'],
            'reservation.fare'                 => ['required', 'numeric', 'min:0'],
        ]);

        $reservation->fill($request->get('reservation'))->save();

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
        } catch (\Exception $e) {
        }

        Toast::info(__('Reservation was saved successfully.'));

        return redirect()->route('platform.reservations');
    }

    /**
     * Delete the reservation.
     *
     * @param Reservation $reservation
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Reservation $reservation)
    {
        $reservation->delete();

        Toast::info(__('Reservation was removed.'));

        return redirect()->route('platform.reservations');
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
}
