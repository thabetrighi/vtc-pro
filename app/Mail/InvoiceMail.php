<?php

namespace App\Mail;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class InvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected Reservation $reservation;
    protected string $lang;

    /**
     * Create a new message instance.
     *
     * @param Reservation $reservation
     * @param string $lang
     */
    public function __construct(Reservation $reservation, string $lang = 'en')
    {
        $this->reservation = $reservation;
        $this->lang = $lang;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your Reservation Invoice', [], $this->lang)
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Set the application locale to the specified language
        App::setLocale($this->lang);

        return new Content(
            view: 'emails.invoice',
            with: [
                'reservation' => $this->reservation,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        // Set the application locale for PDF generation
        App::setLocale($this->lang);

        $pdf = Pdf::loadView('invoice', [
            'reservation' => $this->reservation,
            'generated_at' => now(),
        ]);

        return [
            Attachment::fromData(fn() => $pdf->output(), 'invoice_' . $this->reservation->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
