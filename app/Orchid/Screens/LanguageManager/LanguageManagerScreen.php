<?php

declare(strict_types=1);

namespace App\Orchid\Screens\LanguageManager;

use App\Orchid\Layouts\LanguageManager\LanguageManagerLayout;
use App\Orchid\Layouts\LanguageManager\TranslationEditLayout;
use App\Orchid\Layouts\LanguageManager\TranslationRow;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class LanguageManagerScreen extends Screen
{
    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Language Manager';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Manage translations for different languages';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.language_manager',
        ];
    }

    /**
     * Query the screen's data.
     */
    public function query(): iterable
    {
        $lang = request('lang', 'en');
        $translations = $this->getLanguageFileData($lang);

        return [
            'translations' => $translations,
            'currentLang' => $lang,
        ];
    }

    public function commandBar(): iterable
    {
        $languages = $this->getAvailableLanguages();

        return [
            Link::make(__('Refresh'))
                ->icon('bs.arrow-clockwise')
                ->route('platform.language-manager'),

            DropDown::make(__('Select Language'))
                ->icon('bs.translate')
                ->list(
                    collect($languages)->map(function ($lang) {
                        return Link::make(strtoupper($lang))
                            ->route('platform.language-manager', ['lang' => $lang]);
                    })->all()
                ),
        ];
    }

    /**
     * The screen's layout elements.
     */
    public function layout(): iterable
    {
        return [
            LanguageManagerLayout::class,
            Layout::modal('editTranslationModal', TranslationEditLayout::class)
                ->deferred('loadTranslationOnOpenModal'),
        ];
    }

    /**
     * Loads translation data when opening the modal window.
     *
     * @return array
     */
    public function loadTranslationOnOpenModal($translationKey): iterable
    {
        $lang = request('lang', 'en');
        $translations = $this->getLanguageFileData($lang);
        $translation = data_get($translations, $translationKey);
        $translation = ['key' => $translationKey, 'value' => $translation?->getContent($translationKey)];

        return [
            'translation' => $translation,
        ];
    }

    /**
     * Get the translation file for a specific language.
     */
    /**
     * Get the translation file for a specific language.
     */
    private function getLanguageFileData(string $lang): array
    {
        $path = lang_path("{$lang}/app.php");

        if (!file_exists($path)) {
            return [];
        }

        // Load the translations from the file
        $translations = include $path;

        // Convert the translations into TranslationRow objects
        return collect($translations)
            ->map(function ($value, $key) {
                // Create a TranslationRow object for each key-value pair
                return new TranslationRow($key, $value);
            })
            ->toArray();
    }

    /**
     * Get available languages from the resources/lang directory.
     */
    protected function getAvailableLanguages(): array
    {
        $langPath = resource_path('lang');
        $languages = array_filter(glob($langPath . '/*'), 'is_dir');

        return array_map('basename', $languages);
    }

    public function saveTranslation(Request $request, $translationKey): void
    {
        $request->validate([
            'translation.value' => [
                'required',
                'string'
            ],
        ]);

        $this->updateTranslation([
            'key' => $translationKey,
            'value' => data_get($request->input('translation'), 'value', ''),
        ]);

        Toast::info(__('app.translation_saved'));
    }

    /**
     * Handle the update of a specific translation.
     */
    public function updateTranslation(array $data): void
    {
        $lang = request('lang', 'en');
        $key = $data['key'];
        $value = $data['value'];

        $path = lang_path("{$lang}/app.php");
        $translations = include $path;

        // Update the specific translation key with the new value
        $translations[$key] = $value;

        // Save the updated translations back to the language file
        file_put_contents($path, "<?php\n\nreturn " . var_export($translations, true) . ';');
    }

    /**
     * Handle the update of a specific translation.
     */
    public function changeAppLang($lang)
    {
        if (!in_array($lang, ['en', 'es', 'fr', 'de'])) {
            abort(404);
        }

        // Store the selected language in the session
        session(['locale' => $lang]);

        // Optionally store it in the authenticated user's profile
        if (auth()->check()) {
            $user = auth()->user();
            $user->update(['language' => $lang]); // Add 'language' column to users table
        }

        return redirect()->back();
    }
}
