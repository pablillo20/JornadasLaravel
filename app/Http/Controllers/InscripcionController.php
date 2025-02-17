<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearInscripcionRequest;
use App\Email\TicketMail;
use App\Models\Inscripcion;
use App\Models\Evento;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class InscripcionController extends Controller
{
    public function obtenerInscripcion()
    {
        $inscripcion = Inscripcion::all();

        if ($inscripcion->isEmpty()) {
            $data = [
                'mensaje' => "No hay inscripciones",
                'status' => 200
            ];
            return response()->json($data, 200);
        }

        $data = [
            'inscripcion' => $inscripcion,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function borrarInscripcion($id)
    {
        $inscripcion = Inscripcion::find($id);

        if (!$inscripcion) {
            return response()->json(['message' => 'Inscripcion no encontrado', 'status' => 404], 404);
        }

        $inscripcion->delete();

        return response()->json(['message' => 'Inscripcion eliminado', 'status' => 200], 200);
    }

    public function crearInscripcion(CrearInscripcionRequest $request)
    {
        $inscripcion = Inscripcion::create([
            'user_id' => $request->user_id,
            'evento_id' => $request->evento_id,
            'tipo_inscripcion' => $request->tipo_inscripcion,
        ]);

        $usuario = User::find($request->user_id);
        if ($usuario->es_estudiante) {
            $precioInscripcion = 0;
        } else if ($request->tipo_inscripcion == "presencial") {
            $precioInscripcion = 9;
        } else if ($request->tipo_inscripcion == "virtual") {
            $precioInscripcion = 5;
        } else {
            $precioInscripcion = 9;
        }

        if (!$usuario->es_estudiante) {
            // Crear el pago relacionado con la inscripción
            $pago = Pago::create([
                'cantidad' => $precioInscripcion,
                'estado' => 'completado',
                'user_id' =>  $request->user_id,
            ]);
        } else {
            // Crear el pago relacionado con la inscripción
            $pago = Pago::create([
                'cantidad' => 0,
                'estado' => 'completado',
                'user_id' =>  $request->user_id,
            ]);
        }

        // Obtener usuario
        $evento = Evento::find($request->evento_id);
        

        Mail::to($usuario->email)->send(new TicketMail($precioInscripcion, $usuario->name, $evento->titulo, $evento->id));

        // Respuesta exitosa
        return response()->json(['inscripcion' => $inscripcion, 'pago' => $pago, 'status' => 201], 201);
    }

    public function verificarInscripcion($user_id, $evento_id)
    {
        // Verificar que el evento exista
        $evento = Evento::find($evento_id);
        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado', 'status' => 215], 215);
        }

        // Comprobar si ya se alcanzó el cupo
        $inscritos = Inscripcion::where('evento_id', $evento_id)->count();
        if ($inscritos >= $evento->cupo) {
            return response()->json(['message' => 'Cupo lleno', 'status' => 210], 210);
        }

        // Contar cuántos eventos tipo "taller" tiene el usuario
        $talleres = Inscripcion::where('user_id', $user_id)
            ->whereIn('evento_id', function ($query) {
                $query->select('id')
                    ->from('eventos')
                    ->where('tipo', 'taller');
            })
            ->count();

        // Contar cuántos eventos tipo "conferencia" tiene el usuario
        $conferencias = Inscripcion::where('user_id', $user_id)
            ->whereIn('evento_id', function ($query) {
                $query->select('id')
                    ->from('eventos')
                    ->where('tipo', 'conferencia');
            })
            ->count();

        // Restricción de conferencias y talleres
        if ($evento->tipo === 'conferencia' && $conferencias >= 5) {
            return response()->json(['message' => 'No puedes inscribirte en más de 5 conferencias', 'status' => 220], 220);
        }

        if ($evento->tipo === 'taller' && $talleres >= 4) {
            return response()->json(['message' => 'No puedes inscribirte en más de 4 talleres', 'status' => 221], 221);
        }

        // Verificar si el usuario ya está inscrito en este evento
        $inscrito = Inscripcion::where('user_id', $user_id)
            ->where('evento_id', $evento_id)
            ->exists();

        return response()->json([
            'message' => $inscrito ? 'Ya estás inscrito en este evento' : 'No estás inscrito en este evento',
            'inscrito' => $inscrito,
        ], $inscrito ? 200 : 201);
    }
}
