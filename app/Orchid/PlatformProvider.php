<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make(__('Dashboard'))
                ->icon('bs.house')
                ->route(config('platform.index')),

            // Menu::make(__('Reservations'))
            //     ->icon('bs.ticket-detailed')
            //     ->permission('platform.reservations.list')
            //     ->badge(fn() => 6)
            //     ->list([
            //         Menu::make(__('All Reservations'))
            //             ->route('platform.reservations')
            //             ->permission('platform.reservations.list'),

            //         Menu::make(__('Actual Reservations'))
            //             ->route('platform.reservations.actual')
            //             ->permission('platform.reservations.list'),

            //         Menu::make(__('Past Reservations'))
            //             ->route('platform.reservations.past')
            //             ->permission('platform.reservations.list'),
            //     ]),

            Menu::make(__('Reservation'))
                ->icon('bs.ticket')
                ->route('platform.reservations.create')
                ->permission('platform.reservations.create')
                ->active('*/reservations/create'),

            Menu::make(__('Reservations Management'))
                ->icon('bs.ticket-detailed')
                ->route('platform.reservations')
                ->permission('platform.reservations.list')
                ->active('*/reservations'),

            Menu::make(__('Institutions'))
                ->icon('bs.buildings')
                ->permission('platform.institutions.list')
                ->route('platform.institutions'),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->divider(),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->title(__('Settings'))
                ->permission('platform.systems.roles'),

            Menu::make(__('Settings'))
                ->icon('bs.gear')
                ->route('platform.settings')
                ->permission('platform.systems.roles')
                ->active('*/settings/*'),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),

            ItemPermission::group(__('Reservations'))
                ->addPermission('platform.reservations.list', __('View'))
                ->addPermission('platform.reservations.create', __('Create'))
                ->addPermission('platform.reservations.edit', __('Edit'))
                ->addPermission('platform.reservations.delete', __('Delete')),

            ItemPermission::group(__('Institutions'))
                ->addPermission('platform.institutions.list', __('View'))
                ->addPermission('platform.institutions.create', __('Create'))
                ->addPermission('platform.institutions.edit', __('Edit'))
                ->addPermission('platform.institutions.delete', __('Delete')),
        ];
    }
}
