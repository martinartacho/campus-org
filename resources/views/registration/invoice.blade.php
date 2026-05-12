<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprovant - {{ $registration->registration_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .invoice {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .company-info {
            float: right;
            text-align: right;
            margin-bottom: 20px;
        }
        .client-info {
            margin-bottom: 30px;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .items-table .amount {
            text-align: right;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .total-label {
            width: 200px;
            text-align: right;
            padding-right: 20px;
        }
        .total-value {
            width: 100px;
            text-align: right;
            font-weight: bold;
        }
        .total-final {
            border-top: 2px solid #007bff;
            padding-top: 10px;
            font-size: 18px;
            color: #007bff;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .status-paid {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .status-pending {
            background-color: #ffc107;
            color: #212529;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <!-- Header -->
        <div class="header">
            <h1>COMPROVANT</h1>
            <p><strong>Número:</strong> {{ $registration->registration_code }}</p>
            <p><strong>Fecha de Emisión:</strong> {{ $issue_date }}</p>
            <p><strong>Estado:</strong> 
                @if($registration->payment_status === 'paid')
                    <span class="status-paid">PAGADA</span>
                @else
                    <span class="status-pending">PENDIENTE</span>
                @endif
            </p>
        </div>

        <!-- Company Info -->
        <div class="company-info">
            <strong>Campus Virtual</strong><br>
            Direcció:  {{ env('POSTAL_ADDRESS_CONTACTE', 'Rambla, 4, edifici CTUG') }}<br>
            Teléfon:  {{ env('PHONE_CONTACTE', '+36 xxx xxx xxx') }}<br>
            Email: {{ env('MAIL_ADDRESS_CONTACTE', 'info@campus.org') }}<br>
            CIF: {{ env('CIF_CONTACTE', 'B123456Z') }}
        </div>

        <!-- Clear float -->
        <div style="clear: both;"></div>

        <!-- Client Info -->
        <div class="client-info">
            <h3>Datos del Estudiante</h3>
            <p><strong>Nombre:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
            <p><strong>Email:</strong> {{ $student->email }}</p>
            <p><strong>Teléfono:</strong> {{ $student->phone }}</p>
            <p><strong>DNI:</strong> {{ $student->dni }}</p>
            <p><strong>Código de Estudiante:</strong> {{ $student->student_code }}</p>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <h3>Detalles de la Matriculación</h3>
            <p><strong>Curso:</strong> {{ $course->title }}</p>
            <p><strong>Código del Curso:</strong> {{ $course->code }}</p>
            <p><strong>Temporada:</strong> {{ $season->name }}</p>
            <p><strong>Horas:</strong> {{ $course->hours }} horas</p>
            <p><strong>Fecha de Matriculación:</strong> {{ $registration->registration_date->format('d/m/Y') }}</p>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Horas</th>
                    <th>Precio Unitario</th>
                    <th class="amount">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->hours }}</td>
                    <td class="amount">{{ number_format($registration->amount, 2) }} €</td>
                    <td class="amount">{{ number_format($registration->amount, 2) }} €</td>
                </tr>
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Subtotal:</div>
                <div class="total-value">{{ number_format($registration->amount, 2) }} €</div>
            </div>
            <div class="total-row">
                <div class="total-label">Gastos de gestión:</div>
                <div class="total-value">0,00 €</div>
            </div>
            <div class="total-row total-final">
                <div class="total-label">Total:</div>
                <div class="total-value">{{ number_format($registration->amount, 2) }} €</div>
            </div>
        </div>

        <!-- Payment Info -->
        <div style="margin-top: 30px;">
            <h3>Información de Pago</h3>
            <p><strong>Método de Pago:</strong> Tarjeta de Crédito/Débito</p>
            <p><strong>Estado del Pago:</strong> 
                @if($registration->payment_status === 'paid')
                    <span class="status-paid">PAGADO</span>
                @else
                    <span class="status-pending">PENDIENTE</span>
                @endif
            </p>
            @if($registration->payment_status !== 'paid')
                <p><strong>Fecha Límite de Pago:</strong> {{ $due_date }}</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Esta Comprovant es un documento válido de matriculación en Campus Virtual.</p>
            <p>Per a qualsevol consulta, contacta amb {{ env('MAIL_ADDRESS_CONTACTE', 'info@campus.org') }}</p>
            <p>Campus Virtual - {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
