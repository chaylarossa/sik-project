<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrisisReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'crisis_type_id',
        'urgency_level_id',
        'region_id',
        'occurred_at',
        'description',
        'latitude',
        'longitude',
        'address_text',
        'created_by',
        'verification_status',
        'handling_status',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['crisis_type_id'] ?? null, function (Builder $query, $value) {
                $query->where('crisis_type_id', $value);
            })
            ->when($filters['urgency_level_id'] ?? null, function (Builder $query, $value) {
                $query->where('urgency_level_id', $value);
            })
            ->when($filters['region_id'] ?? null, function (Builder $query, $value) {
                $query->where('region_id', $value);
            })
            ->when($filters['verification_status'] ?? null, function (Builder $query, $value) {
                $query->where('verification_status', $value);
            })
            ->when($filters['handling_status'] ?? null, function (Builder $query, $value) {
                $query->where('handling_status', $value);
            })
            ->when($filters['occurred_from'] ?? null, function (Builder $query, $value) {
                $query->where('occurred_at', '>=', $value);
            })
            ->when($filters['occurred_to'] ?? null, function (Builder $query, $value) {
                $query->where('occurred_at', '<=', $value);
            });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('handling_status', '!=', 'closed');
    }
}
