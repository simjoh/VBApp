<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompletedRegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    private Collection $products;

    /**
     * Create a new message instance.
     */
    public function __construct(private Registration $registration, Collection $products, private Event $event, private string $club, private string $country,private string $startlistlink)
    {
        $this->products = $products;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Final registration received',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        return new Content(
            view: 'Mail.completedregistration-sucess-mail-template',
            with: ['country' => $this->country, 'club' => $this->club, 'startlistlink' => $this->startlistlink, 'registration' => $this->registration, 'adress' => $this->registration->person->adress, 'contact' => $this->registration->person->contactinformation, 'optionals' => $this->products, 'event' => $this->event],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
