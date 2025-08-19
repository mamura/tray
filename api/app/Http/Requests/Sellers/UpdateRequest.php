<?php

namespace App\Http\Requests\Sellers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => is_string($this->name) ? trim($this->name) : $this->name,
            'email' => is_string($this->email) ? mb_strtolower(trim($this->email)) : $this->email,
        ]);
    }

    public function rules(): array
    {
        $sellerId = $this->route('seller')?->id ?? $this->route('seller');

        return [
            'name'  => ['sometimes','required','string','max:120'],
            'email' => [
                'sometimes','required','email',
                Rule::unique('sellers','email')->ignore($sellerId),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'  => 'nome',
            'email' => 'e-mail'
        ];
    }
}
