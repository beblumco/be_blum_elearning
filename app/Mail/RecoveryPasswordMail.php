<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecoveryPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $urlTmp;

    public function __construct($urlTmp)
    {
        $this->urlTmp = $urlTmp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->from('administrador@klaxen.com', 'SAVAK')
        ->subject('Recuperar contraseña')
        ->view('mail.recovery_password');
    }
}
