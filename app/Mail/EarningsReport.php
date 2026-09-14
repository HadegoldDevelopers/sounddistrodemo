<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EarningsReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly string $csvContent,
        public readonly string $filename,
        public readonly string $period,
        public readonly int    $totalStreams,
        public readonly float  $totalEarnings,
        public readonly string $recipientEmail,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Earnings Report: {$this->period}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.earnings-report',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->csvContent, $this->filename)
                ->withMime('text/csv'),
        ];
    }
}