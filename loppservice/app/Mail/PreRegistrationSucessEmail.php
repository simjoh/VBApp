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

class PreRegistrationSucessEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private Registration $registration, private Collection $products, private Event $event, private string $club, private string $country, private string $startlistlink, private string $completeregistrationlink)
    {

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

        $realurl = env('APP_URL');

        return new Content(
            view: 'Mail.preregistration-sucesse-mail-template',
            with: ['country' => $this->country, 'club' => $this->club, 'startlistlink' => $this->startlistlink, 'registration' => $this->registration, 'adress' => $this->registration->person->adress, 'contact' => $this->registration->person->contactinformation, 'optionals' => $this->products, 'event' => $this->event, 'completeregistrationlink' => $this->completeregistrationlink, 'editregistrationdetails' => 'http://localhost:8082/events/' . $this->registration->course_uid . '/registration/' . $this->registration->registration_uid . '/getregitration'],
        );
    }
}
