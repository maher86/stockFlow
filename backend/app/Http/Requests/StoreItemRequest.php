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

class StoreItemRequest extends FormRequest
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
            'package_id' => ['required', 'integer', 'exists:packages,id'],
            'sku' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'season' => ['sometimes', Rule::enum(Season::class)],
            'gender' => ['sometimes', Rule::enum(Gender::class)],
            'type' => ['sometimes', Rule::enum(ItemType::class)],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'conditions' => ['sometimes', 'array'],
            'conditions.*.condition' => ['required_with:conditions', Rule::enum(ItemConditionValue::class)],
            'conditions.*.price_tier' => ['required_with:conditions', Rule::enum(PriceTier::class)],
            'conditions.*.quantity' => ['required_with:conditions', 'integer', 'min:1'],
        ];
    }
}
