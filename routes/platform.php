<?php

declare(strict_types=1);

use App\Orchid\Screens\Settings\SettingActionsScreen;
use App\Orchid\Screens\Settings\SettingFieldsAdvancedScreen;
use App\Orchid\Screens\Settings\SettingFieldsScreen;
use App\Orchid\Screens\Settings\SettingScreen;
use App\Orchid\Screens\Settings\SettingTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Institution\InstitutionEditScreen;
use App\Orchid\Screens\Institution\InstitutionListScreen;
use App\Orchid\Screens\Reservation\ReservationEditScreen;
use App\Orchid\Screens\Reservation\ReservationListScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn(Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn(Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));


// Platform > Reservations > Reservation
Route::screen('reservations/{reservation}/edit', ReservationEditScreen::class)
    ->name('platform.reservations.edit')
    ->breadcrumbs(fn(Trail $trail, $reservation) => $trail
        ->parent('platform.reservations')
        ->push(__('Reservation') . ': #' . $reservation->id, route('platform.reservations.edit', $reservation)));

// Platform > Reservations > Create
Route::screen('reservations/create', ReservationEditScreen::class)
    ->name('platform.reservations.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.reservations')
        ->push(__('Create'), route('platform.reservations.create')));

// Platform > Reservations
Route::screen('reservations', ReservationListScreen::class)
    ->name('platform.reservations')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Reservations'), route('platform.reservations')));

// Platform > Reservations
Route::screen('actual-reservations', ReservationListScreen::class)
    ->name('platform.reservations.actual')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Reservations'), route('platform.reservations')));

// Platform > Reservations
Route::screen('past-reservations', ReservationListScreen::class)
    ->name('platform.reservations.past')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Reservations'), route('platform.reservations')));

// Platform > Institutions > Institution
Route::screen('institutions/{institution}/edit', InstitutionEditScreen::class)
    ->name('platform.institutions.edit')
    ->breadcrumbs(fn(Trail $trail, $institution) => $trail
        ->parent('platform.institutions')
        ->push($institution->name, route('platform.institutions.edit', $institution)));

// Platform > Institutions > Create
Route::screen('institutions/create', InstitutionEditScreen::class)
    ->name('platform.institutions.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.institutions')
        ->push(__('Create'), route('platform.institutions.create')));

// Platform > Institutions
Route::screen('institutions', InstitutionListScreen::class)
    ->name('platform.institutions')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Institutions'), route('platform.institutions')));


// Setting...
Route::screen('settings', SettingScreen::class)
    ->name('platform.settings')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Setting Screen'));

Route::screen('/settings/form/fields', SettingFieldsScreen::class)->name('platform.setting.fields');
Route::screen('/settings/form/advanced', SettingFieldsAdvancedScreen::class)->name('platform.setting.advanced');
Route::screen('/settings/form/editors', SettingTextEditorsScreen::class)->name('platform.setting.editors');
Route::screen('/settings/form/actions', SettingActionsScreen::class)->name('platform.setting.actions');

//Route::screen('idea', Idea::class, 'platform.screens.idea');
