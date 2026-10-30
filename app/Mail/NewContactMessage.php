<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewContactMessage extends Mailable
{
    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo mensaje de '.$this->contactMessage->name.' desde la página web',
            replyTo: $this->contactMessage->email ? [new Address($this->contactMessage->email, $this->contactMessage->name)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.new-contact-message');
    }
}
