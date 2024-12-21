<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Institution;

use App\Models\Institution;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class InstitutionListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'institutions';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', __('app.name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Institution $institution) => $institution->name),

            TD::make('zip_code', __('app.zip_code'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Institution $institution) => $institution->zip_code),

            TD::make('city', __('app.city'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Institution $institution) => $institution->city),

            TD::make('number', __('app.number'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Institution $institution) => $institution->number),

            TD::make('street_name', __('app.street_name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Institution $institution) => $institution->street_name),

            TD::make('user_count', __('app.users'))
                ->align(TD::ALIGN_CENTER)
                ->render(function (Institution $institution) {
                    return Link::make('Users (' . $institution->users()->count() . ')')
                        ->route('platform.systems.users', ['filter[institution_id]' => $institution->id])
                        ->icon('bs.people');
                }),

            TD::make(__('app.actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(Institution $institution) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('app.edit'))
                            ->route('platform.institutions.edit', $institution->id)
                            ->icon('bs.pencil'),

                        Button::make(__('app.delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('app.account_delete_warning'))
                            ->method('remove', [
                                'id' => $institution->id,
                            ]),
                    ])),
        ];
    }
}
