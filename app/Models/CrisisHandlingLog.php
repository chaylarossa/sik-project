<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrisisHandlingLog extends Model
{
    use HasFactory;

    public const TYPE_ASSIGNMENT = 'ASSIGNMENT';
    public const TYPE_PROGRESS = 'PROGRESS';
    public const TYPE_STATUS = 'STATUS';

    public const TYPES = [
        self::TYPE_ASSIGNMENT,
        self::TYPE_PROGRESS,
        self::TYPE_STATUS,
    ];

    protected $fillable = [
        'crisis_handling_id',
        'type',
        'payload',
        'created_by',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function handling(): BelongsTo
    {
        return $this->belongsTo(CrisisHandling::class, 'crisis_handling_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
