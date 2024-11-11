<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\Institution;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UserInstitutionLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Select::make('user.institution_id')
                ->fromModel(Institution::class, 'name')
                ->empty()
                ->orderBy('name')
                ->title(__('Institutions'))
                ->help(__('Select the institution this user has access to')),
        ];
    }
}
