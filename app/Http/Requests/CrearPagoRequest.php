<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearPagoRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cantidad' => 'required',
            'estado' => 'required',
            'userId' => 'required|date'  
        ];
    }

    public function messages(): array
    {
        return [
            'cantidad.required' => 'La cantidad es obligatorio.',
            'estado.required' => 'El estado es obligatorio.',
            'userId.required' => 'El usuario es obligatoria.'
        ];
    }
}
