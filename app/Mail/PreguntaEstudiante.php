<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreguntaEstudiante extends Mailable
{
    use Queueable, SerializesModels;

    protected $estudiante;
    protected $capacitacion;

    public function __construct($estudiante, $capacitacion)
    {
        $this->estudiante = $estudiante;
        $this->capacitacion = $capacitacion;
    }

    public function build()
    {
        return $this->from('administrador@klaxen.com', 'SAVAK')
        ->subject('Pregunta Capacitación')
        ->view('mail.pregunta_estudiante')
        ->with('estudiante', $this->estudiante)
        ->with('capacitacion', $this->capacitacion);
    }
}
