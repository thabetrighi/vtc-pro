<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

class GlobalTranslationReplacer extends Command
{
    protected $signature = 'translations:global-replace 
        {--path=app : Starting path to search for files}
        {--extensions=php,blade.php : File extensions to process}';

    protected $description = 'Replace translation strings globally with prefixed keys';

    public function handle()
    {
        $path = base_path($this->option('path'));
        $extensions = explode(',', $this->option('extensions'));

        // Load all the translations from the lang/en/app.php file
        $translations = Lang::getLoader()->load('en', 'app');

        $this->replaceTranslationsRecursively($path, $extensions, $translations);

        $this->info('Global translation replacement completed.');
        return 0;
    }

    protected function replaceTranslationsRecursively($directory, $extensions, $translations)
    {
        $files = File::allFiles($directory);

        foreach ($files as $file) {
            // Check if file extension matches
            if (!$this->matchesExtensions($file, $extensions)) {
                continue;
            }

            $content = File::get($file);
            $modified = false;

            // Patterns to match different translation function styles
            $patterns = [
                // Match __('app.app.app.unknown.Something')
                '/(\s*__\(\s*[\'"])([^\'"]+)([\'"])/',

                // Match trans('app.app.app.unknown.Something')
                '/(\s*trans\(\s*[\'"])([^\'"]+)([\'"])/',

                // Match Lang::get('app.app.app.unknown.Something')
                '/(\s*Lang::get\(\s*[\'"])([^\'"]+)([\'"])/',
            ];

            foreach ($patterns as $pattern) {
                $content = preg_replace_callback($pattern, function ($matches) use (&$modified, $translations) {
                    // Extract the key (translation string)
                    $key = $matches[2];
                    // Check if the translation key exists in lang/en/app.php                    
                    // if (array_key_exists($key, $translations)) {
                    //     // If the key exists, replace it with the corresponding translation key
                    //     return $matches[1] . $key . $matches[3];
                    // }

                    if (in_array($key, $translations)) {
                        // Search for the value in the translations array
                        $key = array_search($key, $translations);
                    }

                    $newKey = 'app' . '.' . $key;
                    logDebug('key', $newKey);

                    // Mark as modified and return the new key
                    $modified = true;
                    return $matches[1] . $newKey . $matches[3];
                }, $content);
            }

            // Write back if modified
            if ($modified) {
                File::put($file, $content);
                $this->info("Updated translations in {$file}");
            }
        }
    }

    protected function matchesExtensions($file, $extensions)
    {
        $fileExtension = $file->getExtension();
        return in_array($fileExtension, $extensions);
    }
}
