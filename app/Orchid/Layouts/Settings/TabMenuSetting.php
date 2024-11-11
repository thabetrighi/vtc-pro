<?php

namespace App\Orchid\Layouts\Settings;

use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Layouts\TabMenu;

class TabMenuSetting extends TabMenu
{
    /**
     * Get the menu elements to be displayed.
     *
     * @return Menu[]
     */
    protected function navigations(): iterable
    {
        return [
            Menu::make('Overview layouts')
                ->route('platform.setting.layouts'),

            Menu::make('Get Started')
                ->route('platform.main'),

            Menu::make('Documentation')
                ->url('https://orchid.software/en/docs'),

            Menu::make('Setting Screen')
                ->route('platform.setting')
                ->badge(fn () => 6),
        ];
    }
}
