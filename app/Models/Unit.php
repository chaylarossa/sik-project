<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'contact_phone',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function handlingAssignments(): HasMany
    {
        return $this->hasMany(HandlingAssignment::class);
    }

    public function crisisReports(): BelongsToMany
    {
        return $this->belongsToMany(CrisisReport::class, 'crisis_report_unit')
            ->withPivot(['assigned_by', 'assigned_at', 'note'])
            ->withTimestamps();
    }
}
