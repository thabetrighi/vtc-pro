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
            TD::make('name', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Institution $institution) => $institution->name),

            TD::make('zip_code', __('ZIP Code'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Institution $institution) => $institution->zip_code),

            TD::make('city', __('City'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Institution $institution) => $institution->city),

            TD::make('number', __('Number'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Institution $institution) => $institution->number),

            TD::make('street_name', __('Street Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(Institution $institution) => $institution->street_name),

            TD::make('user_count', __('Users'))
                ->align(TD::ALIGN_CENTER)
                ->render(function (Institution $institution) {
                    return Link::make('Users (' . $institution->users()->count() . ')')
                        ->route('platform.systems.users', ['filter[institution_id]' => $institution->id])
                        ->icon('bs.people');
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(Institution $institution) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Edit'))
                            ->route('platform.institutions.edit', $institution->id)
                            ->icon('bs.pencil'),

                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            ->method('remove', [
                                'id' => $institution->id,
                            ]),
                    ])),
        ];
    }
}
