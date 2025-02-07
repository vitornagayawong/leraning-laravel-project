<?php

namespace App\Mail;

use Barryvdh\DomPDF\PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MensagemTesteMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $pdf;

    public function __construct(PDF $pdf)
    {
        $this->pdf = $pdf;
    }

    public function build()
    {
        $pdfContent = $this->pdf->output();  // Gera o conteúdo do PDF como string binária

        return $this->markdown('emails.mensagem-teste')
            ->attachData($pdfContent, 'invoice.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
