<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ponente extends Model
{
    use HasFactory;

    protected $table = "ponentes";

    protected $fillable = [
        'nombre',
        'foto',
        'experiencia',
        'redes_sociales'
    ];
}
