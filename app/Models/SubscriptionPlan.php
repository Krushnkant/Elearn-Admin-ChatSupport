<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $table = 'subscription_plans';

    protected $guarded = [];

    /**
     * Normalized [['text' => ..., 'enabled' => bool], ...] list, decoded from
     * the `features` column. Understands the current JSON-per-row format and
     * falls back to the original "one plain line per feature" format (all
     * treated as enabled) for rows saved before the include/exclude toggle
     * was added.
     */
    public function getFeatureListAttribute()
    {
        $decoded = json_decode($this->attributes['features'] ?? '', true);

        if (is_array($decoded)) {
            $rows = array_filter($decoded, function ($row) {
                return isset($row['text']) && trim($row['text']) !== '';
            });
            return array_values(array_map(function ($row) {
                return ['text' => $row['text'], 'enabled' => !empty($row['enabled'])];
            }, $rows));
        }

        $lines = array_filter(array_map('trim', explode("\n", (string) ($this->attributes['features'] ?? ''))));
        return array_values(array_map(function ($line) {
            return ['text' => $line, 'enabled' => true];
        }, $lines));
    }
}
