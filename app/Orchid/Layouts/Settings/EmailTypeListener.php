<?php

namespace App\Orchid\Layouts\Settings;

use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

class EmailTypeListener extends Listener
{
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = [
        'settings.email_type',
    ];

    /**
     * Layout fields that will be displayed based on the listener.
     *
     * @return array
     */
    protected function layouts(): array
    {
        return [
            Layout::rows([
                Select::make('settings.email_type')
                    ->title('Email Type')
                    ->required()
                    ->options([
                        'default' => 'Default (Env)',
                        'custom' => 'Custom',
                    ])
                    ->help('Choose email configuration method'),

                // SMTP fields that will be displayed conditionally
                Group::make([
                    Input::make('settings.smtp_host')
                        ->title('SMTP Host')
                        ->placeholder('Enter SMTP host')
                        ->canSee($this->query->get('settings.email_type') === 'custom'),

                    Input::make('settings.smtp_port')
                        ->title('SMTP Port')
                        ->type('number')
                        ->placeholder('Enter SMTP port')
                        ->canSee($this->query->get('settings.email_type') === 'custom'),
                ]),

                Group::make([
                    Input::make('settings.smtp_username')
                        ->title('SMTP Username')
                        ->placeholder('Enter SMTP username')
                        ->canSee($this->query->get('settings.email_type') === 'custom'),

                    Input::make('settings.smtp_password')
                        ->title('SMTP Password')
                        ->type('password')
                        ->placeholder('Enter SMTP password')
                        ->canSee($this->query->get('settings.email_type') === 'custom'),
                ]),
            ]),
        ];
    }

    /**
     * Handle method to update the repository based on the input changes.
     *
     * @param Repository $repository
     * @param Request $request
     * @return Repository
     */
    public function handle(Repository $repository, Request $request): Repository
    {
        // Update the email_type field to track changes
        $emailType = $request->input('settings.email_type', 'default');
        return $repository->set('settings.email_type', $emailType);
    }
}
