<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use App\Enums\ItemType;
use App\Enums\Season;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property Season $season
 * @property Gender $gender
 * @property ItemType $type
 */
class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'package_id',
        'sku',
        'name',
        'season',
        'gender',
        'type',
        'quantity',
        'notes',
    ];

    /**
     * @return BelongsTo<Package, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * @return HasMany<ItemCondition, $this>
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(ItemCondition::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'season' => Season::class,
            'gender' => Gender::class,
            'type' => ItemType::class,
        ];
    }
}
