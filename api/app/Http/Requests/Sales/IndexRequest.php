<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class IndexRequest extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    public function rules(): array
    {
        return [
            'seller_id' => ['sometimes','integer','exists:sellers,id'],
            'date_from' => ['sometimes','date_format:Y-m-d'],
            'date_to'   => ['sometimes','date_format:Y-m-d'],
            'page'      => ['sometimes','integer','min:1'],
            'per_page'  => ['sometimes','integer','min:1','max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $from = $this->input('date_from');
            $to   = $this->input('date_to');
            if ($from && $to && $from > $to) {
                $v->errors()->add('date_to', 'date_to deve ser maior ou igual a date_from.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'seller_id' => 'vendedor',
            'date_from' => 'data inicial',
            'date_to'   => 'data final'
        ];
    }
}
