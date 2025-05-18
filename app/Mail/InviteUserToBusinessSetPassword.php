<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteUserToBusinessSetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;

    public function __construct(User $user, string $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been invited to join a business',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invite-user-set-password',
            with: [
                'user' => $this->user,
                'url' => $this->url,
                'inviterName' => auth()->user()->name,
                'businessName' => auth()->user()->currentBusiness->name,
            ],
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
