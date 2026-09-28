<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentSetting extends Model
{
    protected $table = 'content_settings';

    protected $guarded = [];

    /**
     * All settings whose key starts with "{$group}.", keyed by the part after
     * the dot — e.g. group('mock_test') => ['title' => ..., 'badge_1' => ...].
     */
    public static function group(string $group): array
    {
        return static::where('key', 'like', $group . '.%')
            ->pluck('value', 'key')
            ->mapWithKeys(function ($value, $key) use ($group) {
                return [substr($key, strlen($group) + 1) => $value];
            })
            ->all();
    }

    public static function setValue(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
