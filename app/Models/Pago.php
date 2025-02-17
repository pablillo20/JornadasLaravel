<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = "pagos";

    protected $fillable = [
        'cantidad',
        'estado',
        'user_id',
    ];

    public function getCantidad()
    {
        return $this->cantidad;
    }
}
