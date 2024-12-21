<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LanguageSwitcher extends Component
{
    /**
     * List of supported languages.
     *
     * @var array
     */
    public $languages;

    /**
     * Current selected language.
     *
     * @var string
     */
    public $currentLanguage;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // List of available languages, you can customize this
        $this->languages = ['en' => 'English', 'es' => 'Spanish', 'fr' => 'French', 'de' => 'German'];
        $this->currentLanguage = app()->getLocale();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.language-switcher');
    }
}
