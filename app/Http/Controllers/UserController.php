<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function obtenerUser()
    {
        $usuario = User::all();

        if ($usuario->isEmpty()) {
            $data = [
                'mensaje' => "No hay usuarios",
                'status' => 200
            ];
            return response()->json($data, 200);
        }

        $data = [
            'usuario' => $usuario,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function actualizarEsEstudiante(Request $request, $id)
    {
        // Buscar el usuario por ID
        $usuario = User::find($id);

        // Verificar si el usuario existe
        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado',
                'status' => 404
            ], 404);
        }

        // Actualizar solo el campo específico
        $usuario->es_estudiante = $request->input('es_estudiante', 1); 
        $usuario->save();

        return response()->json([
            'mensaje' => 'Usuario actualizado correctamente',
            'usuario' => $usuario,
            'status' => 200
        ], 200);
    }
}
