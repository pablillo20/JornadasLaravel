<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearPonenteRequest;
use App\Models\Ponente;

class PonenteController extends Controller
{
    public function obtenerPonentes()
    {

        // paginacion ==== $ponente = Ponente::paginate(1);

        $ponente = Ponente::all();

        if ($ponente->isEmpty()) {
            $data = [
                'mensaje' => "No hay ponentes",
                'status' => 200
            ];
            return response()->json($data, 200);
        }

        $data = [
            'ponente' => $ponente,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function borrarPonentes($id)
    {
        $ponente = Ponente::find($id);

        if (!$ponente) {
            return response()->json(['message' => 'Ponente no encontrado', 'status' => 404], 404);
        }

        $ponente->delete();

        return response()->json(['message' => 'Ponente eliminado', 'status' => 200], 200);
    }

    public function crearPonente(CrearPonenteRequest $request)
    {
        $fotoPath = null;

        if ($request->has('foto')) {
            if ($request->file('foto')) {
                $request->validate([
                    'foto' => 'image|mimes:jpg,jpeg,png,gif|max:2048'
                ]);
                $fotoPath = $request->file('foto')->store('img', 'public');
            } else {
                $fotoPath = $request->input('foto');
            }
        }

        // Creación del ponente
        try {
            $ponente = Ponente::create([
                'nombre' => $request->nombre,
                'foto' => $fotoPath, 
                'experiencia' => $request->experiencia,
                'redes_sociales' => $request->redes_sociales
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar un ponente: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }

        return response()->json([
            'ponente' => $ponente,
            'status' => 201
        ], 201);
    }




    public function editarPonente(CrearPonenteRequest $request, $id)
    {

        $ponente = Ponente::find($id);

        if (!$ponente) {
            return response()->json(['message' => 'Ponente no encontrado', 'status' => 404], 404);
        }

        $ponente->update($request->all());

        return response()->json(['ponente' => $ponente, 'status' => 200], 200);
    }
}
