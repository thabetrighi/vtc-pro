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
            Menu::make(__('app.dashboard'))
                ->icon('bs.house')
                ->route(config('platform.index')),

            // Menu::make(__('app.reservations'))
            //     ->icon('bs.ticket-detailed')
            //     ->permission('platform.reservations.list')
            //     ->badge(fn() => 6)
            //     ->list([
            //         Menu::make(__('app.all_reservations'))
            //             ->route('platform.reservations')
            //             ->permission('platform.reservations.list'),

            //         Menu::make(__('app.actual_reservations'))
            //             ->route('platform.reservations.actual')
            //             ->permission('platform.reservations.list'),

            //         Menu::make(__('app.past_reservations'))
            //             ->route('platform.reservations.past')
            //             ->permission('platform.reservations.list'),
            //     ]),

            Menu::make(__('app.reservation'))
                ->icon('bs.ticket')
                ->route('platform.reservations.create')
                ->permission('platform.reservations.create')
                ->active('*/reservations/create'),

            Menu::make(__('app.reservations_management'))
                ->icon('bs.ticket-detailed')
                ->route('platform.reservations')
                ->permission('platform.reservations.list')
                ->active('*/reservations'),

            Menu::make(__('app.institutions'))
                ->icon('bs.buildings')
                ->permission('platform.institutions.list')
                ->route('platform.institutions'),

            Menu::make(__('app.users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->divider(),

            Menu::make(__('app.roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->title(__('app.settings'))
                ->permission('platform.systems.roles'),

            Menu::make(__('app.settings'))
                ->icon('bs.gear')
                ->route('platform.settings')
                ->permission('platform.systems.roles')
                ->active('*/settings/*'),

            Menu::make(__('app.language_manager'))
                ->icon('bs.gear')
                ->route('platform.language-manager')
                ->permission('platform.systems.language_manager')
                ->active('*/language-manager/*'),
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
            ItemPermission::group(__('app.system'))
                ->addPermission('platform.systems.roles', __('app.roles'))
                ->addPermission('platform.systems.users', __('app.users'))
                ->addPermission('platform.systems.language_manager', __('app.language_manager')),

            ItemPermission::group(__('app.reservations'))
                ->addPermission('platform.reservations.list', __('app.view'))
                ->addPermission('platform.reservations.create', __('app.create'))
                ->addPermission('platform.reservations.edit', __('app.edit'))
                ->addPermission('platform.reservations.delete', __('app.delete')),

            ItemPermission::group(__('app.institutions'))
                ->addPermission('platform.institutions.list', __('app.view'))
                ->addPermission('platform.institutions.create', __('app.create'))
                ->addPermission('platform.institutions.edit', __('app.edit'))
                ->addPermission('platform.institutions.delete', __('app.delete')),
        ];
    }
}
