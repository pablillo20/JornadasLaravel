<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearEventoRequest;
use App\Models\Evento;


class EventoController extends Controller
{
    public function obtenerEvento()
    {
        $evento = Evento::all();

        if ($evento->isEmpty()) {
            $data = [
                'mensaje' => "No hay eventos",
                'status' => 200
            ];
            return response()->json($data, 200);
        }

        $data = [
            'evento' => $evento,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function borrarEvento($id)
    {
        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado', 'status' => 404], 404);
        }

        $evento->delete();

        return response()->json(['message' => 'Evento eliminado', 'status' => 200], 200);
    }

    public function crearEvento(CrearEventoRequest $request)
    {
        $hora_inicio = $request->fecha . ' ' . $request->hora;
        $hora_limite = date('Y-m-d H:i:s', strtotime($hora_inicio . " +55 minutes"));

        // Verificar si la hora está en los rangos prohibidos (12:00 a 1:00 o 5:00 a 6:00)
        $hora = date('H:i', strtotime($hora_inicio));
        $hora_fin = date('H:i', strtotime($hora_limite));

        // Definir los rangos de horas prohibidas (12:00-13:00 y 17:00-18:00)
        $hora_inicio_prohibida1 = '12:00';
        $hora_fin_prohibida1 = '13:00';

        $hora_inicio_prohibida2 = '17:00';
        $hora_fin_prohibida2 = '18:00';

        // Verificar si la hora de inicio está dentro de los rangos prohibidos
        if (
            ($hora >= $hora_inicio_prohibida1 && $hora < $hora_fin_prohibida1) ||
            ($hora >= $hora_inicio_prohibida2 && $hora < $hora_fin_prohibida2)
        ) {
            return response()->json(['message' => 'No se pueden crear eventos entre las 12:00 PM y 1:00 PM ni entre las 5:00 PM y 6:00 PM debido a que el evento comienza en el tramo de descanso.', 'status' => 400], 400);
        }

        // Verificar si la hora de fin está dentro de los rangos prohibidos
        if (
            ($hora_fin >= $hora_inicio_prohibida1 && $hora_fin < $hora_fin_prohibida1) ||
            ($hora_fin >= $hora_inicio_prohibida2 && $hora_fin < $hora_fin_prohibida2)
        ) {
            return response()->json(['message' => 'No se pueden crear eventos entre las 12:00 PM y 1:00 PM ni entre las 5:00 PM y 6:00 PM debido a que el evento termina en el tramo de descanso.', 'status' => 400], 400);
        }

        // Verificar si el ponente ya tiene un evento en ese horario o si existe un solapamiento
        $eventoExistente = Evento::where('ponente_id', $request->ponente_id)
            ->where('fecha', $request->fecha)
            ->where(function ($query) use ($hora_inicio, $hora_limite) {
                $query->where('hora', $hora_inicio)
                    ->orWhereBetween('hora', [$hora_inicio, $hora_limite])
                    ->orWhereRaw('? BETWEEN hora AND DATE_ADD(hora, INTERVAL duracion MINUTE)', [$hora_inicio]);
            })
            ->exists();

        if ($eventoExistente) {
            return response()->json(['message' => 'El ponente ya está ocupado en ese horario o hasta 55 minutos después.', 'status' => 400], 400);
        }



        if ($eventoExistente) {
            return response()->json(['message' => 'El ponente ya está ocupado en ese horario.', 'status' => 400], 400);
        }

        $hora_inicioEvento = $request->fecha . ' ' . $request->hora;
        $hora_limiteEvento = date('Y-m-d H:i:s', strtotime($hora_inicioEvento . " +55 minutes"));

        // Verificar si ya existe un evento del mismo tipo en el horario solicitado o en el rango de 55 minutos
        $eventoTipoExistente = Evento::where('tipo', $request->tipo)
            ->whereDate('fecha', $request->fecha)
            ->where(function ($query) use ($hora_inicioEvento, $hora_limiteEvento) {
                $query->where('hora', $hora_inicioEvento)
                    ->orWhereBetween('hora', [$hora_inicioEvento, $hora_limiteEvento])
                    ->orWhereRaw('? BETWEEN hora AND DATE_ADD(hora, INTERVAL duracion MINUTE)', [$hora_inicioEvento]);
            })
            ->exists();

        if ($eventoTipoExistente) {
            return response()->json(['message' => 'Ya existe otro evento del mismo tipo a esa hora o en los 55 minutos siguientes.', 'status' => 400], 400);
        }


        // Verificar que la fecha no sea pasada
        $fechaEvento = $request->fecha;
        $fechaActual = date('Y-m-d');
        if ($fechaEvento < $fechaActual) {
            return response()->json(['message' => 'No se pueden crear eventos en fechas pasadas.', 'status' => 400], 400);
        }

        // Verificar que sea jueves o viernes
        $diaSemana = date('l', strtotime($fechaEvento));
        if ($diaSemana !== 'Thursday' && $diaSemana !== 'Friday') {
            return response()->json(['message' => 'El evento solo puede ser creado los jueves o viernes.', 'status' => 400], 400);
        }

        // Crear el evento
        $evento = Evento::create($request->all());

        return $evento
            ? response()->json(['evento' => $evento, 'status' => 201], 201)
            : response()->json(['message' => 'Error al registrar el evento.', 'status' => 500], 500);
    }
}
