<?php

namespace App\Notifications;

use App\Http\Controllers\PdfController;
use App\Mail\newLaravelTips;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Facade;

class notificaUser extends Notification
{
    use Queueable;

    private $user;
    private $pdf;

    public function __construct(User $user, $pdf)
    {
        $this->user = $user;
        $this->pdf = $pdf;
    }
   
    public function via($notifiable)
    {
        return ['mail'];
    }

   
    public function toMail($notifiable)
    {
        

        return (new MailMessage)
                    ->line('Enviando email de teste AGORAAAA')
                    ->subject('Testando notificação')
                    ->greeting('Olá' . $this->user->name)
                    ->action('Entre no sistema', url('/'))
                    ->line('Thank you for using our application!');
                    // ->attach($this->pdf, [
                    //     'as' => 'pdf.pdf',
                    //     'mime' => 'application/pdf'
                    // ]);
                    
                    
                    
    }

    
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    
}
