<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => ['sometimes', 'min:1', 'string'],
            'options' => ['sometimes', 'array', 'min:3'],
            'options.*.text' => ['sometimes', 'string'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.min' => 'É obrigatório ter no mínimo uma pergunta',
            'question.string' => 'A pergunta deve ser um texto',

            'options.min' => 'A enquete deve ter no mínimo 3 opções de voto',

            'end_date.after_or_equal' => 'A data de término deve ser posterior ou igual a data de início',
        ];
    }
}
