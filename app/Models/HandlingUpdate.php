<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HandlingUpdate extends Model
{
    use HasFactory;

    public const STATUSES = CrisisReport::STATUSES;

    protected $fillable = [
        'report_id',
        'updated_by',
        'status',
        'progress_percent',
        'note',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(CrisisReport::class, 'report_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
