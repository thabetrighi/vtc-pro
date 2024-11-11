<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

if (!function_exists('settings')) {
    /**
     * Retrieve a setting or all settings.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function settings($key = null, $default = null)
    {
        $settings = App::make('settings');

        if (is_null($key)) {
            return $settings; // Return all settings
        }

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('logDebug')) {
    /**
     * Log a debug message.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    function logDebug(string $message, array $context = []): void
    {
        Log::debug($message, $context);
    }
}

if (!function_exists('getFile')) {
    /**
     * Retrieve a file URL from storage, with an optional default fallback.
     *
     * @param string $path
     * @param string|null $default
     * @return string
     */
    function getFile(?string $path, string $default = null): ?string
    {
        // Check if the file exists in storage
        if (!empty($path)) {
            return url($path);
        }

        // Return default path or a placeholder
        return $default;
    }
}

if (!function_exists('getStorageFile')) {
    /**
     * Retrieve a file URL from storage, with an optional default fallback.
     *
     * @param string $path
     * @param string|null $default
     * @return string
     */
    function getStorageFile(?string $path, string $default = null): ?string
    {
        // Check if the file exists in storage
        if (!empty($path)) {
            return storage_path(str_replace('storage', 'app/public', $path));
        }

        // Return default path or a placeholder
        return $default;
    }
}
