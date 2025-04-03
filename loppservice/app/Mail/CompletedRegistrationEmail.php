<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Person;
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
    private Registration $registration;
    private Event $event;
    private string $club;
    private string $country;
    private string $startlistlink;
    private string $updatelink;
    private Person $person;
    private Organizer $organizer;
    private string $dnslink;
    /**
     * Create a new message instance.
     */
    public function __construct(Registration $registration, Collection $products, Event $event, string $club, string $country, string $startlistlink, string $updatelink, Person $person, Organizer $organizer, $dnslink)
    {
        $this->person = $person;
        $this->updatelink = $updatelink;
        $this->startlistlink = $startlistlink;
        $this->country = $country;
        $this->club = $club;
        $this->event = $event;
        $this->registration = $registration;
        $this->products = $products;
        $this->organizer = $organizer;
        $this->dnslink = $dnslink;
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
            with: ['organizer' => $this->organizer->organization_name ,'country' => $this->country, 'club' => $this->club, 'startlistlink' => $this->startlistlink, 'registration' => $this->registration, 'adress' => $this->person->adress, 'contact' => $this->person->contactinformation, 'optionals' => $this->products, 'event' => $this->event, 'updatelink' => $this->updatelink, 'person' => $this->person, 'dnslink' => $this->dnslink],
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
