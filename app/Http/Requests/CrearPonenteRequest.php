<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearPonenteRequest extends FormRequest
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
            'nombre' => 'required',
            'foto' => 'required',
            'experiencia' => 'required',
            'redes_sociales' => 'required'
        ];
    }

    public function message(): array{
        return [
            'nomnbre.required' => 'Nombre obligatorio',
            'foto.required' => 'Foto de perfil obligatorioa',
            'experiencia' => 'Experiencia obligatoria',
            'redes_sociales.required' => 'Añade tu red Social'
        ];
    }
}
