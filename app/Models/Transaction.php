<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Transaction extends Model
{
    use HasFactory;

    public function getCreatedAtDateAttribute($value) {
        return date('Y-m-d', strtotime($this->created_at));
    }

    public function getStartDateAttribute2($value) {
        return date('Y-m-d', strtotime($this->created_at));
    }

    public function getEndDateAttribute2($value) {
        return date('Y-m-d ', strtotime($this->created_at));
    }

    public function getPlanAttribute($value)
    {
        switch ($value) {
            default: return null;
            case 1: return 'Free';
            case 2: return 'Silver';
            case 3: return 'Gold';
        }
    }

    // Raw plan id, bypassing getPlanAttribute() above (which only understands
    // the legacy 3-tier Free/Silver/Gold names used by the paywall gating in
    // QuestionController). Used by the 5-tier subscription_plans catalogue.
    public function getPlanIdAttribute()
    {
        return $this->attributes['plan'] ?? null;
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan');
    }
}
