<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ItemConditionValue;
use App\Enums\PriceTier;
use Database\Factories\ItemConditionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ItemConditionValue $condition
 * @property PriceTier $price_tier
 */
class ItemCondition extends Model
{
    /** @use HasFactory<ItemConditionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_id',
        'condition',
        'price_tier',
        'quantity',
    ];

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'condition' => ItemConditionValue::class,
            'price_tier' => PriceTier::class,
        ];
    }
}
