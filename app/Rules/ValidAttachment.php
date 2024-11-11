<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Orchid\Attachment\Models\Attachment;

class ValidAttachment implements ValidationRule
{
    protected array $allowedExtensions;
    protected int $maxSize;

    /**
     * Create a new rule instance.
     *
     * @param  array  $allowedExtensions
     * @param  int  $maxSize (in bytes)
     */
    public function __construct(array $allowedExtensions, int $maxSize)
    {
        $this->allowedExtensions = $allowedExtensions;
        $this->maxSize = $maxSize;
    }

    /**
     * Validate the input value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $attachment = null;

        if (is_int($value)) {
            // Treat $value as an ID and try to find the attachment
            $attachment = Attachment::find($value);
        } elseif (is_string($value) && str_contains($value, '/storage/')) {
            // Treat $value as a file path and extract the name from the filename
            $name = pathinfo($value, PATHINFO_FILENAME);
            $attachment = Attachment::where('name', $name)->first();
        }

        if (!$attachment) {
            $fail("The file specified in {$attribute} does not exist.");
            return;
        }

        // Check file extension
        if (!in_array(strtolower($attachment->extension), $this->allowedExtensions)) {
            $fail("The file extension for {$attribute} must be one of: " . implode(', ', $this->allowedExtensions) . '.');
        }

        // Check file size
        if ($attachment->size > $this->maxSize) {
            $fail("The file size for {$attribute} must not exceed " . ($this->maxSize / 1024 / 1024) . ' MB.');
        }
    }
}
