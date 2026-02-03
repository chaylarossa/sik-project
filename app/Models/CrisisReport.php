<?php

namespace App\Models;

use App\Models\HandlingUpdate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public const VERIFICATION_STATUSES = [
        self::VERIFICATION_PENDING,
        self::VERIFICATION_APPROVED,
        self::VERIFICATION_REJECTED,
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
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

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

    public function handlingAssignments(): HasMany
    {
        return $this->hasMany(HandlingAssignment::class, 'report_id');
    }

    public function handlingUpdates(): HasMany
    {
        return $this->hasMany(HandlingUpdate::class, 'report_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['crisis_type_id'] ?? null, fn (Builder $q, $value) => $q->where('crisis_type_id', $value))
            ->when($filters['verification_status'] ?? null, fn (Builder $q, $value) => $q->where('verification_status', $value))
            ->when($filters['status'] ?? null, fn (Builder $q, $value) => $q->where('status', $value))
            ->when($filters['region_id'] ?? null, fn (Builder $q, $value) => $q->where('region_id', $value))
            ->when($filters['period']['from'] ?? null, fn (Builder $q, $from) => $q->where('occurred_at', '>=', $from))
            ->when($filters['period']['to'] ?? null, fn (Builder $q, $to) => $q->where('occurred_at', '<=', $to));
    }
}
