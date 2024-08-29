<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificarCuentaActivaMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $fullname;
    public string $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $fullname, string $url)
    {
        $this->fullname = $fullname;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('administrador@klaxen.com', 'SAVAK')
        ->subject('Cuenta activada - SAVAK')
        ->view('mail.cuenta_activada');
    }
}
