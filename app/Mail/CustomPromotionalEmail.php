<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomPromotionalEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $messageBody;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectLine, $messageBody)
    {
        $this->subjectLine = $subjectLine;
        $this->messageBody = $messageBody;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(config('mail.from.address', 'hello@trafficvai.com'), config('mail.from.name', 'TrafficVai')),
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        $logo = \App\Models\Setting::get('site_logo');
        $logoUrl = $logo ? asset($logo) : (config('app.url') . '/images/logo.png');

        return new Content(
            view: 'emails.v2.universal_v2',
            with: [
                'title' => $this->subjectLine,
                'message' => $this->messageBody,
                'body' => $this->messageBody,
                'tag' => 'SPECIAL OFFER',
                'user_name' => 'Valued Client',
                'logo_url' => $logoUrl,
                'dashboard_portal_url' => url('/dashboard'),
            ]
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
