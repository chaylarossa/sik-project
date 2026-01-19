<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CrisisType;
use App\Models\UrgencyLevel;
use App\Models\Region;
use App\Models\User;

class CrisisReport extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';
    public const STATUS_CLOSED = 'closed';

    public const VERIFICATION_PENDING = 'pending';
    public const VERIFICATION_APPROVED = 'approved';
    public const VERIFICATION_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_IN_PROGRESS,
        self::STATUS_DONE,
        self::STATUS_CLOSED,
    ];

    protected $fillable = [
        'crisis_type_id',
        'urgency_level_id',
        'region_id',
        'created_by',
        'status',
        'verification_status',
        'occurred_at',
        'address',
        'latitude',
        'longitude',
        'description',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['crisis_type_id'] ?? null, fn (Builder $q, $value) => $q->where('crisis_type_id', $value))
            ->when($filters['urgency_level_id'] ?? null, fn (Builder $q, $value) => $q->where('urgency_level_id', $value))
            ->when($filters['region_id'] ?? null, fn (Builder $q, $value) => $q->where('region_id', $value))
            ->when($filters['status'] ?? null, fn (Builder $q, $value) => $q->where('status', $value))
            ->when($filters['occurred_from'] ?? null, fn (Builder $q, $value) => $q->where('occurred_at', '>=', $value))
            ->when($filters['occurred_to'] ?? null, fn (Builder $q, $value) => $q->where('occurred_at', '<=', $value));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_CLOSED);
    }

    public function crisisType(): BelongsTo
    {
        return $this->belongsTo(CrisisType::class);
    }

    public function urgencyLevel(): BelongsTo
    {
        return $this->belongsTo(UrgencyLevel::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifications()
    {
        return $this->hasMany(Verification::class, 'crisis_report_id');
    }

    public function latestVerification()
    {
        return $this->hasOne(Verification::class, 'crisis_report_id')->latestOfMany();
    }
}
