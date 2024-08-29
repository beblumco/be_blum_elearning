<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevoUsuarioRegistroAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $fullname;
    public string $url;
    public string $company;
    public string $job;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $fullname, string $url, string $company, string $job)
    {
        $this->fullname = $fullname;
        $this->url      = $url;
        $this->company  = $company;
        $this->job      = $job;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('administrador@klaxen.com', 'SAVAK')
        ->subject('Nuevo usuario - SAVAK')
        ->view('mail.register_new_user_admin');
    }
}
