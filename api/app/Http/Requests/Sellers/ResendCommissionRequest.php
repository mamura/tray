<?php

namespace App\Http\Requests\Sellers;

use Illuminate\Foundation\Http\FormRequest;

class ResendCommissionRequest extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'date' => $this->date ?: now()->toDateString(),
        ]);
    }

    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today'
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'date'=>'data'
        ];
    }
}
