<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Institution;

use App\Models\Institution;
use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class InstitutionEditScreen extends Screen
{
    /**
     * @var Institution
     */
    public $institution;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @param Institution $institution
     * @return array
     */
    public function query(Institution $institution): iterable
    {
        return [
            'institution' => $institution,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return $this->institution->exists ? 'Edit Institution' : 'Create Institution';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Manage institution details, including address and other relevant information.';
    }

    /**
     * Permission required to access this screen.
     */
    public function permission(): ?iterable
    {
        return [
            'platform.institutions.create',
            'platform.institutions.edit',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('app.remove'))
                ->icon('bs.trash3')
                ->confirm(__('app.account_delete_confirmation'))
                ->method('remove')
                ->canSee($this->institution->exists),

            Button::make(__('app.save'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * Define the layout for this screen.
     *
     * @return array
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Group::make([
                    Input::make('institution.name')
                        ->title(__('app.name'))
                        ->required()
                        ->placeholder(__('app.enter_institution_name')),

                    Input::make('institution.zip_code')
                        ->title(__('app.zip_code'))
                        ->placeholder(__('app.enter_zip_code')),
                ]),

                Group::make([
                    Input::make('institution.city')
                        ->title(__('app.city'))
                        ->placeholder(__('app.enter_city_name')),

                    Input::make('institution.number')
                        ->title(__('app.building_number'))
                        ->placeholder(__('app.enter_building_number')),
                ]),

                Group::make([
                    Input::make('institution.street_name')
                        ->title(__('app.street_name'))
                        ->placeholder(__('app.enter_street_name')),
                ]),

                Group::make([
                    Input::make('institution.created_at')
                        ->title(__('app.created_at'))
                        ->readonly()
                        ->canSee($this->institution->exists),

                    Input::make('institution.updated_at')
                        ->title(__('app.updated_at'))
                        ->readonly()
                        ->canSee($this->institution->exists),
                ]),

            ])
        ];
    }

    /**
     * Save institution details.
     *
     * @param Institution $institution
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Institution $institution, Request $request)
    {
        $request->validate([
            'institution.name' => ['required', 'string', 'max:255'],
            'institution.zip_code' => ['nullable', 'string', 'max:255'],
            'institution.city' => ['nullable', 'string', 'max:255'],
            'institution.number' => ['nullable', 'string', 'max:255'],
            'institution.street_name' => ['nullable', 'string', 'max:255'],
        ]);

        $institution->fill($request->get('institution'))->save();

        Toast::info(__('app.institution_saved'));

        return redirect()->route('platform.institutions');
    }

    /**
     * Delete the institution.
     *
     * @param Institution $institution
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Institution $institution)
    {
        $institution->delete();

        Toast::info(__('app.institution_removed'));

        return redirect()->route('platform.institutions');
    }
}
