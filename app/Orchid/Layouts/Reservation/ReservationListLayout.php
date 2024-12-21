<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Reservation;

use App\Models\Reservation;
use App\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ReservationListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'reservations';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('id', __('app.id'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('mode', __('app.transport_mode'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('status', __('app.status'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(function (Reservation $reservation) {
                    // Define a color map for different statuses
                    $statusColors = [
                        'pending'   => 'warning',
                        'ongoing'   => 'info',
                        'completed' => 'success',
                        'canceled'  => 'danger',
                    ];

                    // Get the status color or default to 'secondary'
                    $color = $statusColors[$reservation->status] ?? 'secondary';

                    // Render the status with a badge style
                    return "<span class='badge bg-{$color}'>" . ucfirst($reservation->status) . "</span>";
                }),


            TD::make('pickup_location', __('app.pickup'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('destination_location', __('app.destination'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('passenger_name', __('app.passenger'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('departure_at', __('app.departure_time'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make('fare', __('app.fare'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('user', __('app.booked_by'))
                ->render(function (Reservation $reservation) {
                    return optional($reservation->user)->name;
                })
                ->sort()
                ->cantHide()
                ->filter(Relation::make('user_id')->fromModel(User::class, 'name')),

            TD::make(__('app.actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Reservation $reservation) {
                    return DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('app.edit'))
                                ->route('platform.reservations.edit', $reservation->id)
                                ->icon('bs.pencil'),

                            Button::make(__('app.view_invoice'))
                                ->method('viewInvoice', [
                                    'reservation' => $reservation->id,
                                ])
                                ->icon('bs.eye')
                                ->rawClick()
                                ->target('_blank'),

                            Button::make(__('app.send_invoice_email'))
                                ->method('sendInvoiceEmail', [
                                    'reservation' => $reservation->id,
                                ])
                                ->icon('bs.envelope')
                                ->rawClick(),

                            Button::make(__('app.view_note'))
                                ->method('viewInvoice', [
                                    'reservation' => $reservation->id,
                                ])
                                ->icon('bs.eye')
                                ->rawClick()
                                ->target('_blank'),

                            Button::make('app.send_note_email')
                                ->method('sendInvoiceEmail', [
                                    'reservation' => $reservation->id,
                                ])
                                ->icon('bs.envelope')
                                ->rawClick(),

                            // Cancel Reservation Button
                            Button::make(__('app.cancel_reservation'))
                                ->icon('bs.x-circle')
                                ->confirm(__('app.confirm_cancel_reservation'))
                                ->method('cancelReservation', [
                                    'id' => $reservation->id,
                                ]),

                            Button::make(__('app.delete'))
                                ->icon('bs.trash3')
                                ->confirm(__('app.reservation_delete_warning'))
                                ->method('removeReservation', [
                                    'id' => $reservation->id,
                                ]),
                        ]);
                }),
        ];
    }
}
