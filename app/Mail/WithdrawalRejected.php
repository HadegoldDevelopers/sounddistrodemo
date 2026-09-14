<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $withdrawal;
    public $user;
    public $recipientEmail;

    public function __construct($withdrawal, $user)
    {
        $this->withdrawal     = $withdrawal;
        $this->user           = $user;
        $this->recipientEmail = $user->email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Withdrawal Rejected: $' . number_format($this->withdrawal->amount, 2),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.withdrawal-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}