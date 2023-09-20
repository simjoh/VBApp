<?php

namespace App\Mail;

use App\Models\Optional;
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
    public function __construct(private Registration $registration, Collection $optional)
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
            subject: 'Midnigth sun randonee event registration',
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
            with: ['name' => $this->registration->person->firstname, 'completeregistrationlink' => 'http://localhost:8082/events/' . $this->registration->course_uid . '/registration/' . $this->registration->registration_uid . '/complete', 'editregistrationdetails' => 'http://localhost:8082/events/' . $this->registration->course_uid . '/registration/' . $this->registration->registration_uid . '/getregitration'],
        );
    }
}
