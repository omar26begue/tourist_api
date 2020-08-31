<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UsersReset extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;

        $this->subject = 'Su contraseña ha sido restaurada Tourist <'.$email.'>';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reset-users');
    }
}
