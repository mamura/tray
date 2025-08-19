<?php

namespace App\Http\Requests\Sellers;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        return [
            'name'  => ['required','string','max:120'],
            'email' => ['required','email','unique:sellers,email'],
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
