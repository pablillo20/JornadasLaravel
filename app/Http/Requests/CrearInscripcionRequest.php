<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearInscripcionRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'evento_id' => 'required|exists:eventos,id',
            'tipo_inscripcion' => 'required|in:virtual,presencial', 
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio.',
            'evento_id.required' => 'El evento es obligatorio.',
            'tipo_inscripcion.required' => 'El tipo de asistencia es obligatorio.',
            'tipo_inscripcion.in' => 'El tipo de asistencia debe ser uno de los siguientes: virtual, presencial.', 
        ];
    }

}
