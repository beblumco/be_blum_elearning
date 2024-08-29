<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevoUsuarioRegistroMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $fullname;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $fullname)
    {
        $this->fullname = $fullname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('administrador@klaxen.com', 'SAVAK')
        ->subject('Bienvenido a SAVAK')
        ->view('mail.register_new_user');
    }
}
