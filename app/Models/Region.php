<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Region extends Model
{
    use HasFactory;

    public const LEVEL_PROVINCE = 'province';
    public const LEVEL_CITY = 'city';
    public const LEVEL_DISTRICT = 'district';
    public const LEVEL_VILLAGE = 'village';

    public const LEVELS = [
        self::LEVEL_PROVINCE,
        self::LEVEL_CITY,
        self::LEVEL_DISTRICT,
        self::LEVEL_VILLAGE,
    ];

    public const LEVEL_LABELS = [
        self::LEVEL_PROVINCE => 'Provinsi',
        self::LEVEL_CITY => 'Kota/Kabupaten',
        self::LEVEL_DISTRICT => 'Kecamatan',
        self::LEVEL_VILLAGE => 'Kelurahan/Desa',
    ];

    protected $fillable = [
        'name',
        'code',
        'level',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', $level);
    }

    public function scopeChildrenOf(Builder $query, ?int $parentId): Builder
    {
        return $query->where('parent_id', $parentId);
    }

    public static function optionsForLevel(string $level, ?int $parentId = null): Collection
    {
        return self::query()
            ->where('level', $level)
            ->when(!is_null($parentId), fn (Builder $query) => $query->where('parent_id', $parentId))
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);
    }

    public static function parentLevelFor(string $level): ?string
    {
        return match ($level) {
            self::LEVEL_CITY => self::LEVEL_PROVINCE,
            self::LEVEL_DISTRICT => self::LEVEL_CITY,
            self::LEVEL_VILLAGE => self::LEVEL_DISTRICT,
            default => null,
        };
    }

    public function ancestors(): Collection
    {
        $ancestors = collect();
        $current = $this->parent;

        while ($current) {
            $ancestors->push($current);
            $current = $current->parent;
        }

        return $ancestors;
    }

    public static function labelForLevel(string $level): string
    {
        return self::LEVEL_LABELS[$level] ?? $level;
    }
}
