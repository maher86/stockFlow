<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Enums\ItemConditionValue;
use App\Enums\ItemType;
use App\Enums\PriceTier;
use App\Enums\Season;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkUpdateItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:items,id'],
            'items.*.package_id' => ['sometimes', 'integer', 'exists:packages,id'],
            'items.*.sku' => ['sometimes', 'nullable', 'string', 'max:255'],
            'items.*.name' => ['sometimes', 'required', 'string', 'max:255'],
            'items.*.season' => ['sometimes', Rule::enum(Season::class)],
            'items.*.gender' => ['sometimes', Rule::enum(Gender::class)],
            'items.*.type' => ['sometimes', Rule::enum(ItemType::class)],
            'items.*.quantity' => ['sometimes', 'integer', 'min:1'],
            'items.*.notes' => ['sometimes', 'nullable', 'string'],
            'items.*.conditions' => ['sometimes', 'array'],
            'items.*.conditions.*.condition' => ['required_with:items.*.conditions', Rule::enum(ItemConditionValue::class)],
            'items.*.conditions.*.price_tier' => ['required_with:items.*.conditions', Rule::enum(PriceTier::class)],
            'items.*.conditions.*.quantity' => ['required_with:items.*.conditions', 'integer', 'min:1'],
        ];
    }
}
