<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;


class BRMCompletedRegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    private Collection $products;
    private Registration $registration;
    private Event $event;
    private string $club;
    private string $country;
    private string $startlistlink;
    private string $updatelink;
    private Person $person;


    /**
     * Create a new message instance.
     */
    public function __construct(Registration $registration, Collection $products, Event $event, string $club, string $country, string $startlistlink, string $updatelink, Person $person)
    {
        $this->person = $person;
        $this->updatelink = $updatelink;
        $this->startlistlink = $startlistlink;
        $this->country = $country;
        $this->club = $club;
        $this->event = $event;
        $this->registration = $registration;
        $this->products = $products;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'VSRS 2024: bekräftelse på anmälan',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail.brmcompletedregistration-sucess-mail-template',
            with: ['country' => $this->country, 'club' => $this->club, 'startlistlink' => $this->startlistlink, 'registration' => $this->registration, 'adress' => $this->person->adress, 'contact' => $this->person->contactinformation, 'optionals' => $this->products, 'event' => $this->event, 'updatelink' => $this->updatelink, 'person' => $this->person],
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
