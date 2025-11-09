<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // For now allow everyone; add auth logic here later if needed
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id'           => ['required', 'exists:customers,id'],
            'status'                => ['nullable', 'in:pending,paid,shipped,cancelled'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.product_id'    => ['required', 'exists:products,id'],
            'items.*.quantity'      => ['required', 'integer', 'min:1'],
        ];
    }
}
