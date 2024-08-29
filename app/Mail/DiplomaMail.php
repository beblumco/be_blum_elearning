<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DiplomaMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $certificado;
    protected $pdfFilePath;

    public function __construct($certificado, $pdfFilePath)
    {
        $this->certificado = $certificado;
        $this->pdfFilePath = $pdfFilePath;
    }

    public function build()
    {
        return $this->from('administrador@klaxen.com', 'SAVAK')
        ->subject('Certificado SAVAK')
        // ->view('mail.register_new_user')
        ->view('mail.diploma')
            ->attach($this->pdfFilePath, ['as' => $this->certificado->nombre . '.pdf']);
    }
}
