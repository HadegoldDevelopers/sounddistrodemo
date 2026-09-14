<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReleaseRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $recipientEmail;

    public function __construct($project)
    {
        $this->project = $project;
        $this->recipientEmail = $project->user->email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
           subject: "Release Rejected: {$this->project->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.release-rejected',
            with: [
                'project' => $this->project,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}