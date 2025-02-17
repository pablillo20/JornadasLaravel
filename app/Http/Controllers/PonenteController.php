<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearPonenteRequest;
use App\Models\Ponente;

class PonenteController extends Controller
{
    public function obtenerPonentes(){

        // paginacion ==== $ponente = Ponente::paginate(1);

        $ponente = Ponente::all();

        if($ponente->isEmpty()){
            $data = [
                'mensaje' => "No hay ponentes",
                'status' => 200
            ];
            return response()->json($data,200);
        }

        $data = [
            'ponente' => $ponente,
            'status' => 200
        ];

        return response()->json($data,200);
    }

    public function borrarPonentes($id) {
        $ponente = Ponente::find($id);

        if (!$ponente) {
            return response()->json(['message' => 'Ponente no encontrado', 'status' => 404], 404);
        }

        $ponente->delete();

        return response()->json(['message' => 'Ponente eliminado', 'status' => 200], 200);
    }

    public function crearPonente(CrearPonenteRequest $request)
    {
        $ponente = Ponente::create([
            'nombre' => $request->nombre,
            'foto' => $request->foto,
            'experiencia' => $request->experiencia,
            'redes_sociales' => $request->redes_sociales
        ]);

        if(!$ponente) {
            $data = [
                'message' => 'Error al registrar un ponente',
                'status' => 500
            ];
            return response()->json($data,500);
        }

        $data = [
            'ponente' => $ponente,
            'status' => 201
        ];

        return response()->json($data,201);
    }

    public function editarPonente(CrearPonenteRequest $request, $id) {

        $ponente = Ponente::find($id);

        if (!$ponente) {
            return response()->json(['message' => 'Ponente no encontrado', 'status' => 404], 404);
        }

        $ponente->update($request->all());

        return response()->json(['ponente' => $ponente, 'status' => 200], 200);
    }

}
