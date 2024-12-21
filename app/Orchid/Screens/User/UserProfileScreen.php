<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Models\Institution;
use App\Orchid\Layouts\User\ProfilePasswordLayout;
use App\Orchid\Layouts\User\UserCarLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserInstitutionLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Fortify\TwoFactorScreenAuthenticatable;

class UserProfileScreen extends Screen
{
    use TwoFactorScreenAuthenticatable;

    /**
     * Fetch data to be displayed on the screen.
     *
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        return [
            'user' => $request->user(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'My Account';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Update your account details such as name, email address and password';
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Back to my account')
                ->novalidate()
                ->canSee(Impersonation::isSwitch())
                ->icon('bs.people')
                ->route('platform.switch.logout'),

            Button::make('Sign out')
                ->novalidate()
                ->icon('bs.box-arrow-left')
                ->route('platform.logout'),
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
                ->description(__("app.update_your_account_profile_information_and_email_address"))
                ->commands(
                    Button::make(__('app.save'))
                        ->type(Color::BASIC())
                        ->icon('bs.check-circle')
                        ->method('save')
                ),

            Layout::block(UserInstitutionLayout::class)
                ->title(__('app.institution_assignment'))
                ->description(__('app.institution_assignment'))
                ->commands(
                    Button::make(__('app.save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->method('save')
                ),

            Layout::block(UserCarLayout::class)
                ->title(__('app.vehicle_information'))
                ->description(__('app.manage_user_vehicle_details_and_assignments'))
                ->commands(
                    Button::make(__('app.save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->method('save')
                ),

            Layout::block(ProfilePasswordLayout::class)
                ->title(__('app.update_password'))
                ->description(__('app.ensure_long_random_password'))
                ->commands(
                    Button::make(__('app.update_password_btn'))
                        ->type(Color::BASIC())
                        ->icon('bs.check-circle')
                        ->method('changePassword')
                ),
        ];
    }

    public function save(Request $request): void
    {
        $request->validate([
            'user.name'  => 'required|string',
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($request->user()),
            ],
            'user.institution_id' => [
                'nullable',
                Rule::exists(Institution::class, 'id'),
            ],
            'user.car.registration_plate' => 'nullable|unique:cars,registration_plate,' . ($request->user()->car ? $request->user()->car->id : 'null'), // validate registration plate
            'user.car.color'              => 'nullable|string',
            'user.car.model'              => 'nullable|string',
            'user.car.brand'              => 'nullable|string',
            'user.car.category'           => 'nullable|string',
            'user.car.seat_number'        => 'nullable|integer',
            'user.car.energy_type'        => 'nullable|string',
        ]);

        $request->user()
            ->fill($request->get('user'))
            ->save();

        // Handle the car (Create or Update)
        $carData = $request->input('user.car', []);

        if ($request->user()->car) {
            // Update the existing car
            $request->user()->car->update($carData);
        } else {
            // Create a new car and associate it with the user
            $carData['user_id'] = $request->user()->id; // Ensure the car is assigned to the user
            $request->user()->car()->create($carData); // Create a new car
        }

        Toast::info(__('app.profile_updated'));
    }

    public function changePassword(Request $request): void
    {
        $guard = config('platform.guard', 'web');
        $request->validate([
            'old_password' => 'required|current_password:' . $guard,
            'password'     => 'required|confirmed|different:old_password',
        ]);

        tap($request->user(), function ($user) use ($request) {
            $user->password = Hash::make($request->get('password'));
        })->save();

        Toast::info(__('app.password_changed'));
    }
}
