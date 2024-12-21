<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Institution;

use Orchid\Platform\Models\User as Institution;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Layouts\Rows;

class InstitutionPasswordLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        /** @var Institution $institution */
        $institution = $this->query->get('institution');

        $placeholder = $institution->exists
            ? __('app.leave_empty_current_password')
            : __('app.enter_password_to_set');

        return [
            Password::make('institution.password')
                ->placeholder($placeholder)
                ->title(__('app.password')),
        ];
    }
}
