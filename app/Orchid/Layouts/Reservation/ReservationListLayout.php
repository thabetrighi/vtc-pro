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
            TD::make('id', __('ID'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('mode', __('Mode'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('pickup_location', __('Pickup Location'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('destination_location', __('Destination Location'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('passenger_name', __('Passenger Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('departure_at', __('Departure Time'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make('fare', __('Fare'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('user', __('Booked By'))
                ->render(function (Reservation $reservation) {
                    return optional($reservation->user)->name;
                })
                ->sort()
                ->cantHide()
                ->filter(Relation::make('user_id')->fromModel(User::class, 'name')),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Reservation $reservation) {
                    return DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.reservations.edit', $reservation->id)
                                ->icon('bs.pencil'),

                            Button::make('Download Invoice')
                                ->method('downloadInvoice', [
                                    'reservation' => $reservation->id,
                                ])
                                ->icon('bs.download')
                                ->rawClick(),

                            Button::make('View Invoice')
                                ->method('viewInvoice', [
                                    'reservation' => $reservation->id,
                                ])
                                ->icon('bs.eye')
                                ->rawClick()
                                ->target('_blank'),

                            Button::make('Send Invoice Email')
                                ->method('sendInvoiceEmail', [
                                    'reservation' => $reservation->id,
                                ])
                                ->icon('bs.envelope')
                                ->rawClick(),
                                
                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm(__('Once the reservation is deleted, all of its data will be permanently deleted. Before deleting the reservation, please ensure that you have downloaded any data or information that you wish to retain.'))
                                ->method('removeReservation', [
                                    'id' => $reservation->id,
                                ]),
                        ]);
                }),
        ];
    }
}
