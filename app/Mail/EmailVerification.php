<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $token
    ) {}

    public function build()
    {
        return $this->view('emails.verification')
                    ->subject('Código de Verificación');
    }
}