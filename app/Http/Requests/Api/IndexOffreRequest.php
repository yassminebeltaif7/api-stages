<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class IndexOffreRequest extends FormRequest
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
            'domaine' => ['sometimes', 'string', 'exists:domaines,slug'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'domaine.string' => 'Le domaine doit être une chaîne de caractères.',
            'domaine.exists' => 'Le domaine demandé n’existe pas.',
        ];
    }
}
