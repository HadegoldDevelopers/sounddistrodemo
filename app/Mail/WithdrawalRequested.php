<?php

namespace App\Mail;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalRequested extends Mailable
{
    use Queueable, SerializesModels;

    public $withdrawal;
    public $admin;
    public $isAdmin;
    public $recipientEmail;

    public function __construct($withdrawal, bool $isAdmin = false, Admin $admin = null)
    {
        $this->withdrawal     = $withdrawal;
        $this->isAdmin        = $isAdmin;
        $this->admin          = $admin;
        $this->recipientEmail = $isAdmin
            ? $admin->email
            : $withdrawal->user->email;
    }

    public function envelope(): Envelope
    {
        $subject = $this->isAdmin
            ? 'Withdrawal Request from ' . $this->withdrawal->user->name
            : 'Your Withdrawal Request Has Been Received';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.withdrawal-requested',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}