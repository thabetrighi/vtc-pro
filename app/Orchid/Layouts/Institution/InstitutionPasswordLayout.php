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
            ? __('Leave empty to keep current password')
            : __('Enter the password to be set');

        return [
            Password::make('institution.password')
                ->placeholder($placeholder)
                ->title(__('Password')),
        ];
    }
}
