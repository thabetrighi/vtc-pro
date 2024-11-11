<?php

namespace App\Orchid\Layouts\Settings;

use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Layouts\TabMenu;

class SettingTabs extends TabMenu
{
    protected function navigations(): iterable
    {
        return [
            Menu::make('General')->route('platform.setting.fields', ['tab' => 'general']),
            Menu::make('Company')->route('platform.setting.fields', ['tab' => 'company']),
            Menu::make('Email SMTP')->route('platform.setting.fields', ['tab' => 'email']),
            Menu::make('Notes')->route('platform.setting.fields', ['tab' => 'notes']),
            Menu::make('Invoice')->route('platform.setting.fields', ['tab' => 'invoice']),
            // Menu::make('Site Settings')->route('platform.setting.fields', ['tab' => 'site']),
        ];
    }
}
