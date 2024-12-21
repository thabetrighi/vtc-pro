<?php

namespace App\Orchid\Screens\Settings;

use App\Models\Setting;
use App\Orchid\Layouts\Settings\EmailTypeListener;
use App\Rules\ValidAttachment;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Group;

class SettingScreen extends Screen
{
    public $name = 'Settings';
    public $description = 'Application settings';

    public function query(): array
    {
        return [
            'settings' => Setting::all()->pluck('value', 'key')->toArray()
        ];
    }

    public function description(): ?string
    {
        return __('app.settings_desc');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('app.save'))
                ->icon('bs.check-circle')
                ->method('saveSettings'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::tabs([
                'General' => $this->generalTab(),
                'Company' => $this->companyTab(),
                'Email SMTP' => $this->emailTab(),
                'Notes' => $this->notesTab(),
                'Invoice' => $this->invoiceTab(),
                // 'Site Settings' => $this->siteSettingsTab(),
            ]),
        ];
    }

    private function generalTab()
    {
        return Layout::rows([
            Group::make([
                Input::make('settings.site_name')
                    ->required()
                    ->title(__('app.site_name'))
                    ->placeholder(__('app.site_name_placeholder'))
                    ->help(__('app.site_name_help')),

                Input::make('settings.site_description')
                    ->title(__('app.site_description'))
                    ->placeholder(__('app.site_description_desc')),
            ]),

            // Group::make([
            //     Input::make('settings.primary_color')
            //         ->title('Primary Color')
            //         ->type('color')
            //         ->value('#3490dc'),

            //     Input::make('settings.secondary_color')
            //         ->title('Secondary Color')
            //         ->type('color')
            //         ->value('#6c757d'),
            // ]),

            Group::make([
                Cropper::make('settings.site_logo')
                    ->title(__('app.site_logo'))
                    ->targetRelativeUrl()
                    ->width(100)
                    ->height(100)
                    ->maxFileSize(2)
                    ->acceptedFiles('image/*'),

                Cropper::make('settings.favicon')
                    ->title(__('app.favicon'))
                    ->targetRelativeUrl()
                    ->maxFileSize(1)
                    ->width(100)
                    ->height(100)
                    ->acceptedFiles('image/*'),
            ]),
        ]);
    }

    private function companyTab()
    {
        return Layout::rows([
            Group::make([
                Input::make('settings.company_name')
                    ->required()
                    ->title(__('app.company_name'))
                    ->placeholder(__('app.company_name_placeholder')),

                Input::make('settings.street_name')
                    ->title(__('app.street_name_and_number')),
            ]),

            Group::make([
                Input::make('settings.zip_code')
                    ->title(__('app.zip_code'))
                    ->mask('999999'),

                Input::make('settings.city')
                    ->title(__('app.city')),
            ]),

            Group::make([
                Cropper::make('settings.company_logo')
                    ->title(__('app.company_logo'))
                    ->targetRelativeUrl()
                    ->maxFileSize(2)
                    ->acceptedFiles('image/*'),

                Cropper::make('settings.company_stamp')
                    ->title(__('app.company_stamp_signature_setting'))
                    ->targetRelativeUrl()
                    ->maxFileSize(2)
                    ->acceptedFiles('image/*'),
            ]),

            Group::make([
                Input::make('settings.vtc_register_number')
                    ->title(__('app.vtc_register_number')),

                Input::make('settings.tva_number')
                    ->title(__('app.tva_number')),
            ]),

            Group::make([
                Input::make('settings.siret_number')
                    ->title(__('app.siret_number')),

                Input::make('settings.company_phone')
                    ->title(__('app.company_phone'))
                    ->mask('+99 999 999 999')
                    ->type('tel'),
            ]),

            Input::make('settings.company_email')
                ->required()
                ->title(__('app.company_email'))
                ->type('email'),
        ]);
    }

    private function emailTab()
    {
        return [
            EmailTypeListener::class,
        ];
    }

    private function notesTab()
    {
        return Layout::rows([
            Group::make([
                TextArea::make('settings.note_general')
                    ->title(__('app.general_note'))
                    ->rows(4),

                TextArea::make('settings.note_invoice')
                    ->title(__('app.invoice_note'))
                    ->rows(4),
            ]),

            Group::make([
                TextArea::make('settings.note_aa')
                    ->title('Note AA')
                    ->rows(4),

                TextArea::make('settings.note_bb')
                    ->title('Note BB')
                    ->rows(4),
            ]),
        ]);
    }

    private function invoiceTab()
    {
        return Layout::rows([
            Group::make([
                Input::make('settings.vat_transfer')
                    ->title(__('app.vat_for_transfer'))
                    ->type('number')
                    ->min(0)
                    ->max(100)
                    ->placeholder('VAT percentage for transfer services'),

                Input::make('settings.vat_ride')
                    ->title(__('app.vat_for_ride'))
                    ->type('number')
                    ->min(0)
                    ->max(100)
                    ->placeholder(__('app.vat_for_ride_place_holder')),
            ]),

            Select::make('settings.invoice_languages')
                ->title(__('app.invoice_languages'))
                ->required()
                ->options([
                    'en' => 'English',
                    'fr' => 'French',
                ])
                ->multiple()
                ->help(__('app.invoice_languages_help')),
        ]);
    }

    private function siteSettingsTab()
    {
        return Layout::rows([
            CheckBox::make('settings.site_disabled')
                ->title('Disable Site')
                ->placeholder('Disable the website for maintenance')
                ->help('When enabled, the site will show a maintenance page to visitors'),
        ]);
    }

    public function saveSettings(Request $request)
    {
        // Define validation rules for each setting field
        $rules = [
            'settings.site_name'           => 'required|string|max:255',
            'settings.site_description'    => 'nullable|string|max:500',
            'settings.primary_color'       => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'settings.secondary_color'     => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',

            // File validation for images (logo and favicon) with ValidAttachment rule
            'settings.site_logo'           => ['nullable', 'string', new ValidAttachment(['jpg', 'jpeg', 'png', 'gif'], 2048 * 1024)], // 2MB max
            'settings.favicon'             => ['nullable', 'string', new ValidAttachment(['ico', 'png'], 1024 * 1024)], // 1MB max

            // Company details
            'settings.company_name'        => 'required|string|max:255',
            'settings.street_name'         => 'nullable|string|max:255',
            'settings.zip_code'            => 'nullable|string',
            'settings.city'                => 'nullable|string|max:255',

            // Company stamp and logo with ValidAttachment rule
            'settings.company_logo'        => ['nullable', 'string', new ValidAttachment(['jpg', 'jpeg', 'png', 'gif', 'svg'], 2048 * 1024)], // 2MB max
            'settings.company_stamp'       => ['nullable', 'string', new ValidAttachment(['jpg', 'jpeg', 'png', 'gif', 'svg'], 2048 * 1024)], // 2MB max

            'settings.vtc_register_number' => 'nullable|string|max:255',
            'settings.tva_number'          => 'nullable|string|max:255',
            'settings.siret_number'        => 'nullable|string|max:14',
            'settings.company_email'       => 'required|email|max:255',
            'settings.company_phone'       => 'nullable|string',

            'settings.email_type'          => 'required|in:default,custom',

            // Conditional SMTP validation when "email_type" is set to "custom"
            'settings.smtp_host'           => 'required_if:settings.email_type,custom|string|max:255',
            'settings.smtp_port'           => 'required_if:settings.email_type,custom|integer|between:1,65535',
            'settings.smtp_username'       => 'required_if:settings.email_type,custom|string|max:255',
            'settings.smtp_password'       => 'required_if:settings.email_type,custom|string|max:255',

            'settings.note_general'        => 'nullable|string|max:500',
            'settings.note_invoice'        => 'nullable|string|max:500',
            'settings.note_aa'             => 'nullable|string|max:500',
            'settings.note_bb'             => 'nullable|string|max:500',

            'settings.vat_transfer'        => 'nullable|numeric|min:0|max:100',
            'settings.vat_ride'            => 'nullable|numeric|min:0|max:100',

            // Invoice languages (must be selected from predefined options)
            'settings.invoice_languages'   => 'required|array',
            'settings.invoice_languages.*' => 'in:en,fr',

            // Site settings
            'settings.site_disabled'       => 'nullable|boolean',
        ];

        // Custom messages (optional) for better user feedback
        $messages = [
            'settings.site_logo.image'            => 'The site logo must be an image file.',
            'settings.favicon.image'              => 'The favicon must be an image file.',
            'settings.company_email.email'        => 'Please enter a valid company email address.',
            'settings.primary_color.regex'        => 'Primary color must be a valid hex color code.',
            'settings.secondary_color.regex'      => 'Secondary color must be a valid hex color code.',
            'settings.company_phone.regex'        => 'Please enter a valid phone number with country code.',
            'settings.smtp_host.required_if'      => 'SMTP host is required for custom email settings.',
            'settings.smtp_port.between'          => 'SMTP port must be between 1 and 65535.',
            'settings.invoice_languages.required' => 'Please select at least one invoice language.',
        ];

        // Run validation
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = $request->get('settings', []);

        // Process each setting and store it in the database
        foreach ($settings as $key => $value) {
            if (in_array($key, ['site_logo', 'favicon', 'company_logo', 'company_stamp']) && $request->hasFile("settings.$key")) {
                $file = $request->file("settings.$key");
                $path = $file->store('settings', 'public'); // Store file in 'public/settings' directory

                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $path]
                );
            } else {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        Cache::forget('all_settings');

        Alert::success(__('app.settings_saved_successfully'));
        return redirect()->route('platform.settings');
    }
}
