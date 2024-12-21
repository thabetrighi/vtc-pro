<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\LanguageManager;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class TranslationEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('translation.value')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('app.translation'))
        ];
    }
}
