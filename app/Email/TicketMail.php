<?php

namespace App\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pago;
    public $usuario;
    public $evento;
    public $eventoid;

    public function __construct($pago, $usuario, $evento, $eventoid)
    {
        $this->pago = $pago;
        $this->usuario = $usuario;
        $this->evento = $evento;
        $this->eventoid = $eventoid;
    }

    public function build()
    {
        return $this->view('ticket');
    }
}
