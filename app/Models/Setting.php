<?php

namespace App\Models;

use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Illuminate\Support\Facades\Cache;
use Orchid\Attachment\Attachable;

class Setting extends Model
{
    use HasFactory, SoftDeletes, AsSource, Filterable, Attachable;

    protected $fillable = [
        'key',
        'value',
    ];

    // Dynamic cast mapping for specific settings keys
    private $dynamicCasts = [
        'invoice_languages' => 'array',
        'site_disabled' => 'boolean',
        // Add other keys here as needed
    ];

    protected $allowedFilters = [
        'id'          => Where::class,
        'key'         => Like::class,
        'value'       => Like::class,
        'updated_at'  => WhereDateStartEnd::class,
        'created_at'  => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'key',
        'value',
        'updated_at',
        'created_at',
    ];

    public static function getSetting($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 60 * 60, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default; // Automatically casted by getValueAttribute
        });
    }

    public static function setSetting($key, $value)
    {
        $setting = static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::put("setting_{$key}", $setting->value, 60 * 60); // Automatically casted by getValueAttribute
    }

    public static function clearCache()
    {
        Cache::forget('all_settings');
        Cache::tags('settings')->flush();
    }

    /**
     * Mutator for 'value' attribute: dynamically casts based on the key.
     */
    public function getValueAttribute($value)
    {
        return $this->applyDynamicCasting($this->key, $value);
    }

    /**
     * Apply dynamic casting based on the key.
     */
    private function applyDynamicCasting($key, $value)
    {
        if (!isset($this->dynamicCasts[$key])) {
            return $value;
        }

        switch ($this->dynamicCasts[$key]) {
            case 'array':
                return is_array($value) ? $value : json_decode($value, true);
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}
