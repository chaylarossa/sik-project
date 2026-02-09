<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrisisHandling extends Model
{
    use HasFactory;

    public const STATUS_BARU = 'BARU';
    public const STATUS_DALAM_PENANGANAN = 'DALAM_PENANGANAN';
    public const STATUS_SELESAI = 'SELESAI';
    public const STATUS_DITUTUP = 'DITUTUP';

    public const ALLOWED_STATUSES = [
        self::STATUS_BARU,
        self::STATUS_DALAM_PENANGANAN,
        self::STATUS_SELESAI,
        self::STATUS_DITUTUP,
    ];

    protected $fillable = [
        'crisis_report_id',
        'status',
        'progress',
        'current_note',
        'started_at',
        'finished_at',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'closed_at' => 'datetime',
        'progress' => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(CrisisReport::class, 'crisis_report_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CrisisHandlingLog::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_DITUTUP;
    }

    public function canUpdateProgress(): bool
    {
        return ! $this->isClosed();
    }

    /**
     * Set status with validation rules.
     * Rules:
     * - Default BARU (initial) -> handled by create
     * - Any -> DALAM_PENANGANAN (when assignment happens, if currently BARU)
     * - SELESAI only if progress = 100
     * - DITUTUP only by Admin/Coordinator (handled by Policy/Request, here we check logic)
     */
    public function setStatusSafely(string $newStatus, ?User $actor = null): void
    {
        if (! in_array($newStatus, self::ALLOWED_STATUSES)) {
            throw new \InvalidArgumentException("Status tidak valid: {$newStatus}");
        }

        if ($this->isClosed()) {
            throw new \DomainException('Tidak dapat mengubah status laporan yang sudah DITUTUP.');
        }

        if ($newStatus === self::STATUS_SELESAI && $this->progress < 100) {
            throw new \DomainException('Status SELESAI hanya dapat diatur jika progress sudah 100%.');
        }

        // Logic for specific status transitions can be added here
        // e.g., if transitioning to IN_PROGRESS, set started_at if null
        if ($newStatus === self::STATUS_DALAM_PENANGANAN && is_null($this->started_at)) {
            $this->started_at = now();
        }

        // if transitioning to SELESAI, set finished_at
        if ($newStatus === self::STATUS_SELESAI && is_null($this->finished_at)) {
            $this->finished_at = now();
        }

        // if transitioning to DITUTUP, set closed_at and closed_by
        if ($newStatus === self::STATUS_DITUTUP) {
            $this->closed_at = now();
            $this->closed_by = $actor?->id;
        }

        $this->status = $newStatus;
    }
}
