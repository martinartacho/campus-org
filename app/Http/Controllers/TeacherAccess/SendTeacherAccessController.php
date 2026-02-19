<?php

namespace App\Http\Controllers\TeacherAccess;

use App\Http\Controllers\Controller;
use App\Mail\TeacherAccessMail;
use App\Models\User;
use App\Models\TeacherAccessToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class SendTeacherAccessController extends Controller
{
    public function send(User $teacher, Request $request)
    {
        $this->authorize('campus.consents.request');

        $token = TeacherAccessToken::create([
            'teacher_id' => $teacher->id,
            // 'season_id'  => $seasonId, // millorar
            // 'course_id'  => $courseId, // millorar
            'token' => Str::uuid(),
            'expires_at' => now()->addDays(7),
        ]);

        $purpose = $request->get('purpose'); // 'consent' | 'payments'

        $courseCode = $request->get('courseCode');

        Mail::to($teacher->email)
            ->send(new TeacherAccessMail(
                $teacher,
                $token,
                $purpose,
                $courseCode ?? 'general'  // Valor por defecto si es null
            ));


        return back()->with('success', 'Recordatori enviat correctament.' . ($courseCode ? ' (' . $courseCode . ')' : ''));
    }
}
