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
            Button::make(__('app.remove'))
                ->icon('bs.trash3')
                ->confirm(__('app.reservation_delete_permanent'))
                ->method('remove')
                ->canSee($this->reservation->exists),

            // Button::make(__('app.save'))
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

            Layout::columns([
                Layout::rows([
                    Group::make([
                        Select::make('reservation.mode')
                            // ->title(__('app.transport_mode'))
                            ->options([
                                'transfer' => __('app.transfer'),
                                'ride' => __('app.ride'),
                            ])
                            ->help(__('app.choose_between_a_transfer_or_ride'))
                            ->required(),
                    ]),
                ])->title(__('app.transport_mode')),

                Layout::rows([
                    Group::make([
                        DateTimer::make('reservation.departure_at')
                            // ->title(__('app.departure_date_and_time'))
                            ->help(__('app.scheduled_pickup_date_and_time'))
                            ->required()
                            ->format24hr()
                            ->enableTime(),
                    ]),
                ])->title(__('app.date_et_heure_de_dpart')),
            ]),

            Layout::columns([
                Layout::rows([
                    Input::make('reservation.pickup_location')
                        ->title(__('app.number_and_street'))
                        // ->help('Main pickup location or landmark')
                        ->required()
                        ->placeholder(__('app.enter_number_and_street')),

                    Group::make([
                        Input::make('reservation.pickup_zip_code')
                            ->title(__('app.zip_code'))
                            // ->help('ZIP/Postal code of pickup location')
                            ->nullable()
                            ->placeholder(__('app.enter_pickup_zip_code')),

                        Input::make('reservation.pickup_city')
                            ->title(__('app.city'))
                            // ->help('City of pickup location')
                            ->nullable()
                            ->placeholder(__('app.enter_pickup_city')),
                    ]),

                    TextArea::make('reservation.pickup_note')
                        ->title(__('app.pickup_note'))
                        ->help(__('app.pickup_note_help'))
                        ->rows(3)
                        ->placeholder(__('app.enter_pickup_note')),

                ])->title(__('app.pickup_locations')),

                Layout::rows([
                    Input::make('reservation.destination_location')
                        ->title(__('app.number_and_street'))
                        // ->help('Main destination location or landmark')
                        ->required()
                        ->placeholder(__('app.enter_number_and_street')),

                    Group::make([
                        Input::make('reservation.destination_zip_code')
                            ->title(__('app.zip_code'))
                            // ->help('ZIP/Postal code of destination')
                            ->nullable()
                            ->placeholder(__('app.enter_destination_zip_code')),

                        Input::make('reservation.destination_city')
                            ->title(__('app.city'))
                            // ->help('City of destination')
                            ->nullable()
                            ->placeholder(__('app.enter_destination_city')),
                    ]),

                    TextArea::make('reservation.destination_note')
                        ->title(__('app.destination_note'))
                        ->help(__('app.destination_note_help'))
                        ->rows(3)
                        ->placeholder(__('app.enter_destination_note')),

                ])->title(__('app.destination_locations')),
            ]),

            Layout::rows([
                Group::make([
                    Input::make('reservation.passenger_name')
                        ->title(__('app.full_name'))
                        ->help(__('app.first_and_last_name_of_the_passenger'))
                        ->required()
                        ->placeholder(__('app.enter_first_last_name')),

                    Input::make('reservation.passenger_email')
                        ->title(__('app.passenger_email'))
                        ->help(__('app.contact_email_for_booking_confirmation'))
                        ->type('email')
                    // ->placeholder(__('app.enter_passenger_email')),
                ]),
                Group::make([
                    Input::make('reservation.passenger_phone')
                        ->title(__('app.passenger_phone'))
                        // ->help('Primary contact number')
                        ->type('tel')
                        ->placeholder(__('app.enter_passenger_phone')),

                    Input::make('reservation.passenger_count')
                        ->title(__('app.passengers_count'))
                        ->help(__('app.total_number_of_passengers'))
                        ->type('number')
                        ->min(1)
                        ->required()
                        ->placeholder(__('app.enter_passenger_count')),
                ]),
                // Note Passenger
                Group::make([
                    TextArea::make('reservation.note_passenger')
                        ->title(__('app.note_passenger'))
                        ->help(__('app.note_passenger_help'))
                        ->rows(3)
                ]),
            ])->title(__('app.passenger_info')),

            Layout::rows([
                // Departure and Payment
                Group::make([
                    Select::make('reservation.payment_method')
                        ->title(__('app.payment_method'))
                        // ->help('Select payment method for the ride')
                        ->options([
                            'cash' => __('app.cash'),
                            'card' => __('app.card'),
                        ])
                        ->required(),

                    Input::make('reservation.fare')
                        ->title(__('app.fare'))
                        // ->help('Total cost of the ride')
                        ->type('number')
                        ->step(0.01)
                        ->required()
                        ->placeholder(__('app.enter_fare')),
                ]),

                // Additional Info
                Group::make([
                    TextArea::make('reservation.additional_info')
                        ->placeholder(__('app.enter_additional_information'))
                        ->readonly()
                        ->disabled()
                        ->rows(3)
                        ->style("max-width: 100%;")
                ]),

                // Timestamps
                Group::make([
                    Input::make('reservation.created_at')
                        ->title(__('app.created_at'))
                        ->readonly()
                        ->canSee($this->reservation->exists),

                    Input::make('reservation.updated_at')
                        ->title(__('app.updated_at'))
                        ->readonly()
                        ->canSee($this->reservation->exists),
                ]),
            ]),

            Layout::rows([
                Group::make([
                    Button::make($this->reservation->exists ? __('app.save') : __('app.reservation'))
                        ->icon('bs.check-circle')
                        ->class('btn btn-black btn-lg btn-link icon-link p-2 px-4 rounded-3')
                        ->method('save'),

                    Button::make(__('app.draft_reservation'))
                        ->icon('bs.check-circle')
                        ->class('btn btn-black btn-lg btn-link icon-link p-2 px-4 rounded-3')
                        ->method('saveDraft')
                        ->canSee(!$this->reservation->exists),
                ])->set('class', 'd-flex justify-content-center'),
            ])
        ];
    }

    /**
     * Save Draft reservation details.
     *
     * @param Reservation $reservation
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveDraft(Reservation $reservation, Request $request, $isDraft = false)
    {
        $this->save($reservation, $request, true);
    }

    /**
     * Save reservation details.
     *
     * @param Reservation $reservation
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Reservation $reservation, Request $request, $isDraft = false)
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

        if ($isDraft) {
            $reservation->status = 'draft';
        }

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

        Toast::info(__('app.reservation_saved'));

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

        Toast::info(__('app.reservation_removed'));

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
