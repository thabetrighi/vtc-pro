<?php

namespace App\Orchid\Layouts\LanguageManager;

class TranslationRow
{
    public $key;
    public $value;

    // Constructor to initialize key and value
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Get the content of the translation.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getContent(string $key)
    {
        // If you want to perform any special logic, add it here
        // For now, just return the value
        return is_array($this->value) ? '' : $this->value;
    }
}
