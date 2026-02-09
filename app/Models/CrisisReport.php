<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrisisReport extends Model
{
    use HasFactory, SoftDeletes;

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
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $from = $filters['occurred_from'] ?? ($filters['period']['from'] ?? null);
        $to = $filters['occurred_to'] ?? ($filters['period']['to'] ?? null);

        return $query
            ->when($filters['crisis_type_id'] ?? null, fn (Builder $q, $value) => $q->where('crisis_type_id', $value))
            ->when($filters['urgency_level_id'] ?? null, fn (Builder $q, $value) => $q->where('urgency_level_id', $value))
            ->when($filters['verification_status'] ?? null, fn (Builder $q, $value) => $q->where('verification_status', $value))
            ->when($filters['region_id'] ?? null, fn (Builder $q, $value) => $q->where('region_id', $value))
            ->when($filters['status'] ?? null, fn (Builder $q, $value) => $q->where('status', $value))
            ->when($from, fn (Builder $q, $value) => $q->where('occurred_at', '>=', $value))
            ->when($to, fn (Builder $q, $value) => $q->where('occurred_at', '<=', $value));
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

    public function media(): HasMany
    {
        return $this->hasMany(CrisisMedia::class);
    }

    public function verifications()
    {
        return $this->hasMany(Verification::class, 'crisis_report_id');
    }

    public function latestVerification()
    {
        return $this->hasOne(Verification::class, 'crisis_report_id')->latestOfMany();
    }

    public function handlingAssignments(): HasMany
    {
        return $this->hasMany(HandlingAssignment::class, 'report_id');
    }

    public function handlingUpdates(): HasMany
    {
        return $this->hasMany(HandlingUpdate::class, 'report_id');
    }

    public function handling(): HasOne
    {
        return $this->hasOne(CrisisHandling::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'crisis_report_unit')
            ->withPivot(['assigned_by', 'assigned_at', 'note'])
            ->withTimestamps();
    }

    public function getHandlingStatusAttribute(): string
    {
        return $this->handling?->status ?? CrisisHandling::STATUS_BARU;
    }
}
