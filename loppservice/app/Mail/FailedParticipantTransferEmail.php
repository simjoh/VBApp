<?php


namespace App\Mail;

use App\Models\ErrorEvents;
use App\Models\Event;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FailedParticipantTransferEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Person $person;
    public Registration $registration;
    public ErrorEvents $errorEvent;
    public Event $event;

    /**
     * Create a new message instance.
     */
    public function __construct(Person $person, Registration $registration, ErrorEvents $errorEvent, Event $event)
    {
        $this->event = $event;
        $this->errorEvent = $errorEvent;
        $this->registration = $registration;
        $this->person = $person;
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Failed Participant Transfer Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail.failed-participantransfer-template'
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
