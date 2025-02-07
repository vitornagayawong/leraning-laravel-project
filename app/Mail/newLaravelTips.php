<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class newLaravelTips extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $this->subject('Novo episódio está no ar!');
        $this->to($this->user->email, $this->user->name);
        //return $this->view('mail.newLaravelTips', ['user' => $this->user]);
        return $this->markdown('mail.newLaravelTips', ['user' => $this->user]); //markdown é tipo uma view mais bonita e está passando uma props lá pro blade newLaravelTips.blade.php
    }
}
