<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreRegistrationSucessEmail extends Mailable
{
    use Queueable, SerializesModels;

    private Registration $registration;
    private Collection $products;
    private Event $event;
    private string $club;
    private string $country;
    private string $startlistlink;
    private string $completeregistrationlink;
    private string $updatelink;
    private Person $person;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Registration $registration, Collection $products, Event $event, string $club, string $country, string $startlistlink, string $completeregistrationlink, string $updatelink, Person $person)
    {
        $this->person = $person;
        $this->updatelink = $updatelink;
        $this->completeregistrationlink = $completeregistrationlink;
        $this->startlistlink = $startlistlink;
        $this->country = $country;
        $this->club = $club;
        $this->event = $event;
        $this->products = $products;
        $this->registration = $registration;

    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Reservation received',
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
            view: 'Mail.preregistration-sucesse-mail-template',
            with: ['country' => $this->country, 'club' => $this->club, 'startlistlink' => $this->startlistlink, 'registration' => $this->registration, 'adress' => $this->person->adress, 'contact' => $this->person->contactinformation, 'optionals' => $this->products, 'event' => $this->event, 'completeregistrationlink' => $this->completeregistrationlink, 'updatelink' => $this->updatelink, 'person' => $this->person],
        );
    }
}
