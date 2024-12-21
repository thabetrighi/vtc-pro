<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Platform\Models\Role;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UserRoleLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Select::make('user.roles.')
                ->fromModel(Role::class, 'name')
                ->multiple()
                ->title(__('app.name_role'))
                ->help(__('app.specify_which_groups_this_account_should_belong_to')),
        ];
    }
}
