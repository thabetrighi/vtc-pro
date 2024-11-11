<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class UserCarLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('user.car.color')
                ->title('Color')
                ->placeholder('Enter car color')
                ->help('The color of the car.'),

            Input::make('user.car.model')
                ->title('Model')
                ->placeholder('Enter car model')
                ->help('The model of the car.'),

            Input::make('user.car.brand')
                ->title('Brand')
                ->placeholder('Enter car brand')
                ->help('The brand of the car.'),

            Input::make('user.car.category')
                ->title('Category')
                ->placeholder('Enter car category')
                ->help('The category of the car.'),

            Input::make('user.car.seat_number')
                ->title('Seat Number')
                ->type('number')
                ->placeholder('Enter number of seats')
                ->help('The number of seats in the car.'),

            Input::make('user.car.energy_type')
                ->title('Energy Type')
                ->placeholder('Enter energy type (e.g., Gasoline, Electric)')
                ->help('The type of energy the car uses.'),

            Input::make('user.car.registration_plate')
                ->title('Registration Plate')
                ->placeholder('Enter registration plate')
                ->required()
                ->help('The unique registration plate of the car.'),
        ];
    }
}
