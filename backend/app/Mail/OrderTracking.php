<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OrderTracking extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $orders,
        public string $frontendUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Link tra cứu đơn hàng của bạn - FiveTech Store',
        );
    }

public function content(): Content
    {
        return new Content(
            view: 'emails.order-tracking',
            with: [
                'frontendUrl' => $this->frontendUrl,
            ]
        );
    }
}

