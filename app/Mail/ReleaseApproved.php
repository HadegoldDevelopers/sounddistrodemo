<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReleaseApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $recipientEmail;

    public function __construct($project)
    {
        $this->project        = $project;
        $this->recipientEmail = $project->user->email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Release Approved: {$this->project->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.release-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}