<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Models\Institution;
use App\Models\User;
use App\Orchid\Layouts\Role\RolePermissionLayout;
use App\Orchid\Layouts\User\UserCarLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserInstitutionLayout;
use App\Orchid\Layouts\User\UserPasswordLayout;
use App\Orchid\Layouts\User\UserRoleLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserEditScreen extends Screen
{
    /**
     * @var User
     */
    public $user;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(User $user): iterable
    {
        $user->load(['roles', 'car']);

        return [
            'user'       => $user,
            'permission' => $user->getStatusPermission(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return $this->user->exists ? 'Edit User' : 'Create User';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'User profile and privileges, including their associated role.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
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
            Button::make(__('app.impersonate_user'))
                ->icon('bg.box-arrow-in-right')
                ->confirm(__('app.revert_to_original_state'))
                ->method('loginAs')
                ->canSee($this->user->exists && $this->user->id !== \request()->user()->id),

            Button::make(__('app.remove'))
                ->icon('bs.trash3')
                ->confirm(__('app.account_delete_warning'))
                ->method('remove')
                ->canSee($this->user->exists),

            Button::make(__('app.save'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [

            Layout::block(UserEditLayout::class)
                ->title(__('app.profile_information'))
                ->description(__('app.update_your_account_profile_information_and_email_address'))
                ->commands(
                    Button::make(__('app.save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

            Layout::block(UserInstitutionLayout::class)
                ->title(__('app.institution_assignment'))
                ->description(__('app.institution_assignment'))
                ->commands(
                    Button::make(__('app.save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

            Layout::block(UserCarLayout::class)
                ->title(__('app.vehicle_information'))
                ->description(__('app.manage_user_vehicle_details_and_assignments'))
                ->commands(
                    Button::make(__('app.save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

            Layout::block(UserPasswordLayout::class)
                ->title(__('app.password'))
                ->description(__('app.ensure_long_random_password'))
                ->commands(
                    Button::make(__('app.save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

            Layout::block(UserRoleLayout::class)
                ->title(__('app.roles'))
                ->description(__('app.role_definition'))
                ->commands(
                    Button::make(__('app.save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

            Layout::block(RolePermissionLayout::class)
                ->title(__('app.permissions'))
                ->description(__('app.user_additional_actions'))
                ->commands(
                    Button::make(__('app.save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(User $user, Request $request)
    {
        // Validate User and Car data
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
            'user.institution_id' => [
                'nullable',
                Rule::exists(Institution::class, 'id'),
            ],
            'user.car.registration_plate' => 'nullable|unique:cars,registration_plate,' . ($user->car ? $user->car->id : 'null'), // validate registration plate
            'user.car.color'              => 'nullable|string',
            'user.car.model'              => 'nullable|string',
            'user.car.brand'              => 'nullable|string',
            'user.car.category'           => 'nullable|string',
            'user.car.seat_number'        => 'nullable|integer',
            'user.car.energy_type'        => 'nullable|string',
        ]);

        // Handle permissions
        $permissions = collect($request->get('permissions'))
            ->map(fn($value, $key) => [base64_decode($key) => $value])
            ->collapse()
            ->toArray();

        // Handle password if provided
        $user->when($request->filled('user.password'), function (Builder $builder) use ($request) {
            $builder->getModel()->password = Hash::make($request->input('user.password'));
        });

        // Update the user model data
        $user
            ->fill($request->collect('user')->except(['password', 'permissions', 'roles'])->toArray())
            ->forceFill(['permissions' => $permissions])
            ->save();

        // Replace roles (if provided)
        $user->replaceRoles($request->input('user.roles'));

        // Handle the car (Create or Update)
        $carData = $request->input('user.car', []);

        if ($user->car) {
            // Update the existing car
            $user->car->update($carData);
        } else {
            // Create a new car and associate it with the user
            $carData['user_id'] = $user->id; // Ensure the car is assigned to the user
            $user->car()->create($carData); // Create a new car
        }

        Toast::info(__('app.user_car_info_saved'));

        return redirect()->route('platform.systems.users');
    }

    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(User $user)
    {
        $user->delete();

        Toast::info(__('app.user_removed'));

        return redirect()->route('platform.systems.users');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAs(User $user)
    {
        Impersonation::loginAs($user);

        Toast::info(__('app.impersonating_user'));

        return redirect()->route(config('platform.index'));
    }
}
