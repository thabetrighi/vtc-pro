<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Load settings at the start of the application
        $this->app->singleton('settings', function () {
            return Cache::remember('all_settings', 60 * 60, function () {
                return Setting::all()->pluck('value', 'key')->toArray();
            });
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Initialize settings and configure application
        $this->initSettings();

        // Clear cache when settings table is updated
        Setting::saved(function () {
            Cache::forget('all_settings');
            app()->make('settings'); // Refresh settings in cache
        });
    }

    /**
     * Initialize application settings, overriding config values as needed.
     */
    protected function initSettings(): void
    {
        // Get all settings from cache
        $settings = app('settings');

        // Override application name if "site_name" is set
        if (!empty($settings['site_name'])) {
            Config::set('app.name', $settings['site_name']);
        }

        // Configure mail settings if "email_type" is set to "custom"
        if (isset($settings['email_type']) && $settings['email_type'] === 'custom') {
            Config::set('mail.mailers.smtp.host', $settings['smtp_host'] ?? 'localhost');
            Config::set('mail.mailers.smtp.port', $settings['smtp_port'] ?? 587);
            Config::set('mail.mailers.smtp.username', $settings['smtp_username'] ?? null);
            Config::set('mail.mailers.smtp.password', $settings['smtp_password'] ?? null);
            Config::set('mail.mailers.smtp.encryption', $settings['smtp_encryption'] ?? 'tls');
            Config::set('mail.from.address', $settings['smtp_from_address'] ?? config('mail.from.address'));
            Config::set('mail.from.name', $settings['smtp_from_name'] ?? config('mail.from.name'));
        }

        // Additional dynamic configuration
        // Example: Set site theme colors if present
        if (isset($settings['primary_color'])) {
            Config::set('theme.primary_color', $settings['primary_color']);
        }
        if (isset($settings['secondary_color'])) {
            Config::set('theme.secondary_color', $settings['secondary_color']);
        }

        // Override any additional config values as needed
        // Example: Config::set('your_custom.config_key', $settings['custom_setting_key']);
    }
}
