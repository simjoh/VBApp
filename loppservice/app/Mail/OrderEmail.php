<?php


namespace App\Mail;


use App\Models\Event;
use App\Models\NonParticipantOptionals;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public NonParticipantOptionals $optionals;
    public Event $event;
    public Product $product;

    /**
     * Create a new message instance.
     */
    public function __construct(NonParticipantOptionals $optionals, Event $event, Product $product)
    {
        $this->product = $product;
        $this->event = $event;
        $this->optionals = $optionals;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order received',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail.order-mail-template',
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


