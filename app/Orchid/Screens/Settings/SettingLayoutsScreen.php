<?php

namespace App\Orchid\Screens\Settings;

use App\Orchid\Layouts\Settings\TabMenuSetting;
use Orchid\Screen\Action;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class SettingLayoutsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Layout Overview';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'A comprehensive guide to the different layout options available.';
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @throws \Throwable
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [

            Layout::block(Layout::view('platform::dummy.block'))
                ->title('Block header')
                ->description('Excellent description that editing or views in block'),

            Layout::tabs([
                'Setting Tab 1' => Layout::view('platform::dummy.block'),
                'Setting Tab 2' => Layout::view('platform::dummy.block'),
                'Setting Tab 3' => Layout::view('platform::dummy.block'),
            ]),

            TabMenuSetting::class,
            Layout::view('platform::dummy.block'),

            Layout::columns([
                Layout::view('platform::dummy.block'),
                Layout::view('platform::dummy.block'),
                Layout::view('platform::dummy.block'),
            ]),

            Layout::accordion([
                'Collapsible Group Item #1' => Layout::view('platform::dummy.block'),
                'Collapsible Group Item #2' => Layout::view('platform::dummy.block'),
                'Collapsible Group Item #3' => Layout::view('platform::dummy.block'),
            ]),

        ];
    }
}
