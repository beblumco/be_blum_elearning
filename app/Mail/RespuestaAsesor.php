<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RespuestaAsesor extends Mailable
{
    use Queueable, SerializesModels;

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function build()
    {
        return $this->from('administrador@klaxen.com', 'SAVAK')
        ->subject('Respuesta Capacitación')
        ->view('mail.respuesta_asesor')
        ->with('request', $this->request);
    }
}
