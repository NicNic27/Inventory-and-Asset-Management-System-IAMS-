<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceivePoDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'po_id'     => ['required', 'integer', 'exists:purchase_orders,id'],
            'dr_number' => ['required', 'string', 'max:255'],
            'dr_date'   => ['required', 'date'],
            'remarks'   => ['nullable', 'string', 'max:1000'],
            'items'     => ['required', 'array', 'min:1'],
            'items.*.po_item_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity'   => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.po_item_id.distinct' => 'Each P.O. item can only be received once per delivery.',
            'dr_number.required'          => 'The Delivery Receipt number is required.',
            'dr_date.required'            => 'The delivery date is required.',
        ];
    }
}
