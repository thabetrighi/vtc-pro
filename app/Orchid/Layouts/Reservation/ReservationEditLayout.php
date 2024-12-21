<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Reservation;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class ReservationEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('reservation.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('app.name'))
                ->placeholder(__('app.name')),

            Input::make('reservation.email')
                ->type('email')
                ->required()
                ->title(__('app.email'))
                ->placeholder(__('app.email')),
        ];
    }
}
