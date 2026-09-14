<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReleaseSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $isAdmin;
    public $recipientEmail;

    public function __construct($project, bool $isAdmin = false)
    {
        $this->project        = $project;
        $this->isAdmin        = $isAdmin;
        $this->recipientEmail = $project->user->email;
    }

    public function build()
    {
        $subject = $this->isAdmin
            ? 'New Release Submitted — Action Required'
            : 'We Received Your Release Submission';

        return $this->subject($subject)
                    ->view('emails.release-submitted');
    }
}