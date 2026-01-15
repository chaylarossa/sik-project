<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrisisReport extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';
    public const STATUS_CLOSED = 'closed';

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
}
