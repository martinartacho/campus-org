<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('campus.registration_confirmed_title') }} - {{ __('campus.campus_virtual') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .course-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .student-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        .btn:hover {
            background: #0056b3;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .status-paid {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 {{ __('campus.registration_confirmed_title') }}</h1>
        <p>{{ __('campus.campus_virtual') }}</p>
    </div>

    <div class="content">
        <p>{{ __('campus.hello_student', ['name' => $student->first_name]) }}</p>
        
        <p>{{ __('campus.registration_confirmed_message') }}</p>

        <div class="student-info">
            <h3>📋 {{ __('campus.student_data') }}</h3>
            <p><strong>{{ __('campus.name') }}:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
            <p><strong>{{ __('campus.email') }}:</strong> {{ $student->email }}</p>
            <p><strong>{{ __('campus.student_code') }}:</strong> {{ $student->student_code }}</p>
            <p><strong>{{ __('campus.status') }}:</strong> <span class="status-paid">{{ __('campus.paid') }}</span></p>
        </div>

        <div class="course-info">
            <h3>📚 {{ __('campus.enrolled_course') }}</h3>
            <p><strong>{{ __('campus.course') }}:</strong> {{ $course->title }}</p>
            <p><strong>{{ __('campus.code') }}:</strong> {{ $course->code }}</p>
            <p><strong>{{ __('campus.hours') }}:</strong> {{ $course->hours }} {{ __('campus.hours') }}</p>
            <p><strong>{{ __('campus.price') }}:</strong> {{ number_format($registration->amount, 2) }} €</p>
            <p><strong>{{ __('campus.registration_date') }}:</strong> {{ $registration->registration_date->format('d/m/Y') }}</p>
            <p><strong>{{ __('campus.registration_code') }}:</strong> {{ $registration->registration_code }}</p>
        </div>

        <h3>📄 {{ __('campus.download_invoice') }}</h3>
        <p>{{ __('campus.download_invoice_description') }}</p>
        <p><a href="{{ url('factura/' . $registration->id) }}" class="btn">📥 {{ __('campus.download_invoice_pdf') }}</a></p>

        <h3>🎯 {{ __('campus.next_steps') }}</h3>
        <ul>
            <li>{{ __('campus.check_email_updates') }}</li>
            <li>{{ __('campus.prepare_start_date') }}</li>
            <li>{{ __('campus.save_invoice') }}</li>
            <li>{{ __('campus.if_questions_contact', ['email' => env('MAIL_ADDRESS_CONTACTE', 'info@campus.org')]) }}</li>
        </ul>

        <h3>📞 {{ __('campus.contact_support') }}</h3>
        <p><strong>{{ __('campus.email') }}:</strong> {{ env('MAIL_ADDRESS_CONTACTE', 'info@campus.org') }}</p>
        <p><strong>{{ __('campus.phone') }}:</strong> {{ env('PHONE_CONTACTE', '+34 900 123 456') }}</p>
        <p><strong>{{ __('campus.schedule') }}:</strong> {{ __('campus.work_schedule') }}</p>
    </div>

    <div class="footer">
        <p>{{ __('campus.automatic_email') }}</p>
        <p>&copy; {{ date('Y') }} {{ __('campus.campus_virtual') }}. {{ __('campus.all_rights_reserved') }}.</p>
    </div>
</body>
</html>
