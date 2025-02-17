<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearPagoRequest;
use App\Models\Pago;


class PagoController extends Controller
{
    public function obtenerPagos(){
        $pago = Pago::all();

        if($pago->isEmpty()){
            $data = [
                'mensaje' => "No hay pagos",
                'status' => 200
            ];
            return response()->json($data,200);
        }

        $data = [
            'pago' => $pago,
            'status' => 200
        ];

        return response()->json($data,200);
    }

}
