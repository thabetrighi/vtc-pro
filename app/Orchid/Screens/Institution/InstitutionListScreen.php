<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Institution;

use App\Models\Institution;
use App\Orchid\Layouts\Institution\InstitutionEditLayout;
use App\Orchid\Layouts\Institution\InstitutionFiltersLayout;
use App\Orchid\Layouts\Institution\InstitutionListLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class InstitutionListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'institutions' => Institution::withCount('users')
                ->filters(InstitutionFiltersLayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Institution Management';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'A comprehensive list of all registered institutions, including their profiles and privileges.';
    }

    public function permission(): ?iterable
    {
        return [
            // 'platform.institutions',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->route('platform.institutions.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            InstitutionFiltersLayout::class,
            InstitutionListLayout::class,

            Layout::modal('editInstitutionModal', InstitutionEditLayout::class)
                ->deferred('loadInstitutionOnOpenModal'),
        ];
    }

    /**
     * Loads institution data when opening the modal window.
     *
     * @return array
     */
    public function loadInstitutionOnOpenModal(Institution $institution): iterable
    {
        return [
            'institution' => $institution,
        ];
    }

    public function saveInstitution(Request $request, Institution $institution): void
    {
        $request->validate([
            'institution.email' => [
                'required',
                Rule::unique(Institution::class, 'email')->ignore($institution),
            ],
        ]);

        $institution->fill($request->input('institution'))->save();

        Toast::info(__('Institution was saved.'));
    }

    public function remove(Request $request): void
    {
        Institution::findOrFail($request->get('id'))->delete();

        Toast::info(__('Institution was removed'));
    }
}
