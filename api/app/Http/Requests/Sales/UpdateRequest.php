<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    public function rules(): array
    {
        return [
            'seller_id' => ['sometimes','required','integer','exists:sellers,id'],
            'amount'    => ['sometimes','required','numeric','min:0.01'],
            'sold_at'   => ['sometimes','required','date_format:Y-m-d','before_or_equal:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'seller_id' => 'vendedor',
            'amount'    => 'valor',
            'sold_at'   => 'data da venda'
        ];
    }
}
