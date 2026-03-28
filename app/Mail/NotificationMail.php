<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($notification, $user)
    {
        $this->notification = $notification;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->notification->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'notification' => $this->notification,
                'user' => $this->user,
                'processedContent' => $this->processContentImages($this->notification->content),
            ]
        );
    }

    /**
     * Process content images to make them Gmail-compatible
     */
    private function processContentImages($content)
    {
        // Convert relative image URLs to absolute URLs
        $content = preg_replace_callback(
            '/src="\/([^"]+)"/',
            function($matches) {
                return 'src="' . config('app.url') . '/' . $matches[1] . '"';
            },
            $content
        );
        
        // Convert data URIs to embedded images (optional - more complex)
        // For now, just ensure all images have proper absolute URLs
        
        return $content;
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
