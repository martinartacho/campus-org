<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('campus.voucher') }} - {{ $registration->registration_code }}</title>
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
            <h1>{{ __('campus.voucher') }}</h1>
            <p><strong>{{ __('campus.number') }}:</strong> {{ $registration->registration_code }}</p>
            <p><strong>{{ __('campus.issue_date') }}:</strong> {{ $issue_date }}</p>
            <p><strong>{{ __('campus.status') }}:</strong> 
                @if($registration->payment_status === 'paid')
                    <span class="status-paid">{{ __('campus.paid') }}</span>
                @else
                    <span class="status-pending">{{ __('campus.pending') }}</span>
                @endif
            </p>
        </div>

        <!-- Company Info -->
        <div class="company-info">
            <strong>{{ __('campus.campus_virtual') }}</strong><br>
            {{ __('campus.direction') }}:  {{ env('POSTAL_ADDRESS_CONTACTE', 'Rambla, 4, edifici CTUG') }}<br>
            {{ __('campus.phone') }}:  {{ env('PHONE_CONTACTE', '+36 xxx xxx xxx') }}<br>
            {{ __('campus.email') }}: {{ env('MAIL_ADDRESS_CONTACTE', 'info@campus.org') }}<br>
            CIF: {{ env('CIF_CONTACTE', 'B123456Z') }}
        </div>

        <!-- Clear float -->
        <div style="clear: both;"></div>

        <!-- Client Info -->
        <div class="client-info">
            <h3>{{ __('campus.student_data_invoice') }}</h3>
            <p><strong>{{ __('campus.name') }}:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
            <p><strong>{{ __('campus.email') }}:</strong> {{ $student->email }}</p>
            <p><strong>{{ __('campus.phone') }}:</strong> {{ $student->phone }}</p>
            <p><strong>{{ __('campus.dni') }}:</strong> {{ $student->dni }}</p>
            <p><strong>{{ __('campus.student_code') }}:</strong> {{ $student->student_code }}</p>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <h3>{{ __('campus.registration_details') }}</h3>
            <p><strong>{{ __('campus.course') }}:</strong> {{ $course->title }}</p>
            <p><strong>{{ __('campus.course_code') }}:</strong> {{ $course->code }}</p>
            <p><strong>{{ __('campus.season') }}:</strong> {{ $season->name }}</p>
            <p><strong>{{ __('campus.hours') }}:</strong> {{ $course->hours }} {{ __('campus.hours') }}</p>
            <p><strong>{{ __('campus.registration_date') }}:</strong> {{ $registration->registration_date->format('d/m/Y') }}</p>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>{{ __('campus.description') }}</th>
                    <th>{{ __('campus.hours') }}</th>
                    <th>{{ __('campus.unit_price') }}</th>
                    <th class="amount">{{ __('campus.total') }}</th>
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
                <div class="total-label">{{ __('campus.subtotal') }}:</div>
                <div class="total-value">{{ number_format($registration->amount, 2) }} €</div>
            </div>
            <div class="total-row">
                <div class="total-label">{{ __('campus.management_fees') }}:</div>
                <div class="total-value">0,00 €</div>
            </div>
            <div class="total-row total-final">
                <div class="total-label">{{ __('campus.total') }}:</div>
                <div class="total-value">{{ number_format($registration->amount, 2) }} €</div>
            </div>
        </div>

        <!-- Payment Info -->
        <div style="margin-top: 30px;">
            <h3>{{ __('campus.payment_info') }}</h3>
            <p><strong>{{ __('campus.payment_method') }}:</strong> {{ __('campus.credit_debit_card') }}</p>
            <p><strong>{{ __('campus.payment_status') }}:</strong> 
                @if($registration->payment_status === 'paid')
                    <span class="status-paid">{{ __('campus.paid') }}</span>
                @else
                    <span class="status-pending">{{ __('campus.pending') }}</span>
                @endif
            </p>
            @if($registration->payment_status !== 'paid')
                <p><strong>{{ __('campus.due_date') }}:</strong> {{ $due_date }}</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ __('campus.valid_document') }}</p>
            <p>{{ __('campus.for_any_query', ['email' => env('MAIL_ADDRESS_CONTACTE', 'info@campus.org')]) }}</p>
            <p>{{ __('campus.campus_virtual') }} - {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
