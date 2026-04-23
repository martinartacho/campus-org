<?php

namespace App\Mail;

use App\Models\CampusRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;

    public function __construct(CampusRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmació de Matrícula - ' . $this->registration->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-confirmed',
            with: [
                'registration' => $this->registration,
                'course' => $this->registration->course,
                'student' => $this->registration->student,
                'season' => $this->registration->course->season,
            ]
        );
    }
}
