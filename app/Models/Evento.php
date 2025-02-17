<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $table = "eventos";

    protected $fillable = [
        'titulo',
        'tipo',
        'fecha',
        'hora',
        'duracion',
        'cupo',
        'ponente_id'
    ];

    public function getTitulo()
    {
        return $this->titulo;
    }
}
