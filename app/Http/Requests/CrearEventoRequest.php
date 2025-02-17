<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearEventoRequest extends FormRequest
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
            'titulo' => 'required',
            'tipo' => 'required',
            'fecha' => 'required|date',
            'hora' => 'required',
            'duracion' => 'numeric',
            'cupo' => 'required|numeric',
            'ponente_id' => 'required|exists:ponentes,id'  
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'tipo.required' => 'El tipo de evento es obligatorio.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe tener un formato válido.',
            'hora.required' => 'La hora es obligatoria.',
            'duracion.numeric' => 'La duración debe ser un valor numérico.',
            'cupo.required' => 'El cupo es obligatorio.',
            'cupo.numeric' => 'El cupo debe ser un valor numérico.',
            'ponente_id.required' => 'Debe seleccionar un ponente.',
            'ponente_id.exists' => 'El ponente seleccionado no es válido.'
        ];
    }
}
