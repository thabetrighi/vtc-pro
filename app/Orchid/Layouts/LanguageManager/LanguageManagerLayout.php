<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\LanguageManager;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class LanguageManagerLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'translations';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('key', __('app.key'))
                ->sort()
                ->cantHide()
                ->width(300)
                ->filter(Input::make())
                ->render(function (TranslationRow $translation) {
                    return $translation->key;  // Return the key of the translation
                }),

            TD::make('value', __('app.translation'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(TranslationRow $translation) => ModalToggle::make(is_array($translation->value) ? '' : '  ' . ($translation->value ?: '-') . '  ')
                    ->modal('editTranslationModal')
                    ->modalTitle($translation->key)
                    ->method('saveTranslation')
                    ->asyncParameters([
                        'translationKey' => $translation->key,
                        'lang' => request('lang', 'en'),
                    ])),
            // ->render(function (TranslationRow $translation) {
            //     return $translation->getContent($translation->key);  // Call getContent dynamically
            // }),
        ];
    }
}
