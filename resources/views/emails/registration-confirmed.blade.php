<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Matrícula Confirmada - Campus Virtual</title>
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
        <h1>🎓 Matrícula Confirmada</h1>
        <p>Campus Virtual</p>
    </div>

    <div class="content">
        <p>¡Hola {{ $student->first_name }}!</p>
        
        <p>Tu matrícula ha sido confirmada exitosamente. A continuación encontrarás todos los detalles:</p>

        <div class="student-info">
            <h3>📋 Datos del Estudiante</h3>
            <p><strong>Nombre:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
            <p><strong>Email:</strong> {{ $student->email }}</p>
            <p><strong>Código de Estudiante:</strong> {{ $student->student_code }}</p>
            <p><strong>Estado:</strong> <span class="status-paid">PAGADO</span></p>
        </div>

        <div class="course-info">
            <h3>📚 Curso Matriculado</h3>
            <p><strong>Curso:</strong> {{ $course->title }}</p>
            <p><strong>Código:</strong> {{ $course->code }}</p>
            <p><strong>Sessions:</strong> {{ $course->hours }} sessions</p>
            <p><strong>Precio:</strong> {{ number_format($registration->amount, 2) }} €</p>
            <p><strong>Fecha de Matriculación:</strong> {{ $registration->registration_date->format('d/m/Y') }}</p>
            <p><strong>Código de Matrícula:</strong> {{ $registration->registration_code }}</p>
        </div>

        <h3>📄 Descargar Comprovant</h3>
        <p>Pots descarregar el teu comprovant en qualsevol moment usant el següent enllaç:</p>
        <p><a href="{{ url('factura/' . $registration->id) }}" class="btn">📥 Descarregar Comprovant PDF</a></p>

        <h3>🎯 Próximos Pasos</h3>
        <ul>
            <li>Revisa tu correo electrónico regularmente para actualizaciones del curso</li>
            <li>Prepárate para comenzar en la fecha de inicio del curso</li>
            <li>Guarda aquest comprovant per als teus registres</li>
            <li>Si tienes alguna pregunta, contacta con info@campus.org</li>
        </ul>

        <h3>📞 Contacto y Soporte</h3>
        <p><strong>Email:</strong> info@campus.org</p>
        <p><strong>Teléfono:</strong> +34 900 123 456</p>
        <p><strong>Horario:</strong> L-V: 9:00 - 18:00</p>
    </div>

    <div class="footer">
        <p>Este es un correo automático de Campus Virtual. Por favor no respondas a este mensaje.</p>
        <p>&copy; {{ date('Y') }} Campus Virtual. Todos los derechos reservados.</p>
    </div>
</body>
</html>
