<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacturaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $factura;
    public $pdfPath;
    public $xmlPath;

    public function __construct($factura, $pdfPath, $xmlPath)
    {
        $this->factura = $factura;
        $this->pdfPath = $pdfPath;
        $this->xmlPath = $xmlPath;
    }


    public function build()
    {
        $body = "Estimado cliente, adjuntamos su factura. Gracias por su preferencia.";

        return $this
            ->subject("Factura N° {$this->factura->secuencial}")
            ->from('khipusistemas@gmail.com', 'Nombre de tu empresa')
            ->to($this->factura->customer->email)
            ->html($body) // Aquí defines el contenido del correo
            ->attach($this->pdfPath, [
                'as' => "{$this->factura->secuencial}.pdf",
                'mime' => 'application/pdf',
            ])
            ->attach($this->xmlPath, [
                'as' => "{$this->factura->secuencial}.xml",
                'mime' => 'application/xml',
            ]);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Factura Mail',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
