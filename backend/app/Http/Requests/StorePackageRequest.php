<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'reference' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'total_items' => ['sometimes', 'integer', 'min:0'],
            'received_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
