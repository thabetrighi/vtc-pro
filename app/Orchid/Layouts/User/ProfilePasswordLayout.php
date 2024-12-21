<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Layouts\Rows;

class ProfilePasswordLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Password::make('old_password')
                ->placeholder(__('app.enter_current_password'))
                ->title(__('app.current_password'))
                ->help(__('app.this_is_your_password_set_at_the_moment')),

            Password::make('password')
                ->placeholder(__('app.enter_password_to_set'))
                ->title(__('app.new_password')),

            Password::make('password_confirmation')
                ->placeholder(__('app.enter_password_to_set'))
                ->title(__('app.confirm_new_password'))
                ->help(__('app.a_good_password_is_at_least15_characters_or_at_least8_characters_long_including_a_number_and_a_lowercase_letter')),
        ];
    }
}
