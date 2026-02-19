<?php

namespace App\Mail;

use App\Models\User;
use App\Models\TeacherAccessToken;
use App\Models\CampusCourse;
use Illuminate\Mail\Mailable;



class TeacherAccessMail extends Mailable
{
    public function __construct(
        public User $teacher,
        public TeacherAccessToken $token,
        public string $purpose, // 'consent' | 'payments'
        public string $courseCode,
        //public CampusCourse $course

    ) {}

    public function build()
    {
        return $this
            ->subject($this->subjectForPurpose())
            ->view('emails.teacher-access');
    }

    protected function subjectForPurpose(): string
    {
        return match ($this->purpose) {
            'payments' => 'Dades de professorat de la UPG',
            default    => 'Dades de professorat de la UPG',
        };
    }
}

