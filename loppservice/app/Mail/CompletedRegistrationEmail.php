<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Person;
use App\Models\Registration;
use App\Models\Optional;
use App\Services\VoucherService;
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

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Final registration received',
        );
    }

    public function content(): Content
    {
        // Assign voucher codes for products that need them
        $voucherService = new VoucherService();
        $productIds = $this->products->pluck('productID')->toArray();
        $voucherCodes = $voucherService->assignVouchersForRegistration($this->registration, $productIds);

        return new Content(
            view: 'Mail.completedregistration-sucess-mail-template',
            with: [
                'organizer' => $this->organizer->organization_name,
                'country' => $this->country,
                'club' => $this->club,
                'startlistlink' => $this->startlistlink,
                'registration' => $this->registration,
                'adress' => $this->person->adress,
                'contact' => $this->person->contactinformation,
                'optionals' => $this->products,
                'event' => $this->event,
                'updatelink' => $this->updatelink,
                'person' => $this->person,
                'dnslink' => $this->dnslink,
                'voucherCodes' => $voucherCodes
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
