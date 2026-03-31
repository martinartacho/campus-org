<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perfil del Professor - {{ $teacher->full_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #333;
        }
        .row {
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .value {
            display: inline-block;
        }
        .approved {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
        }
        .not-approved {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Perfil del Professor</h1>
        <p>Data: {{ $date }}</p>
    </div>

    <div class="section">
        <div class="section-title">1. Dades Personals</div>
        <div class="row">
            <span class="label">Nom complet:</span>
            <span class="value">{{ $teacher->first_name }} {{ $teacher->last_name }}</span>
        </div>
        <div class="row">
            <span class="label">DNI:</span>
            <span class="value">{{ $teacher->dni ?? 'No especificat' }}</span>
        </div>
        <div class="row">
            <span class="label">Correu electrònic:</span>
            <span class="value">{{ $teacher->email }}</span>
        </div>
        <div class="row">
            <span class="label">Telèfon:</span>
            <span class="value">{{ $teacher->phone ?? 'No especificat' }}</span>
        </div>
        <div class="row">
            <span class="label">Adreça:</span>
            <span class="value">{{ $teacher->address ?? 'No especificat' }}</span>
        </div>
        <div class="row">
            <span class="label">Codi Postal:</span>
            <span class="value">{{ $teacher->postal_code ?? 'No especificat' }}</span>
        </div>
        <div class="row">
            <span class="label">Ciutat:</span>
            <span class="value">{{ $teacher->city ?? 'No especificat' }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">2. Dades de Cobrament</div>
        <div class="row">
            <span class="label">Tipus de cobrament:</span>
            <span class="value">
                @switch($payment_type)
                    @case('waived')
                        Renunciar al cobrament
                    @case('own')
                        Cobrament propi
                    @case('ceded')
                        Cedir el cobrament
                    @default
                        No especificat
                @endswitch
            </span>
        </div>
        
        @if($payment_type === 'own')
            <div class="row">
                <span class="label">IBAN:</span>
                <span class="value">{{ $teacher->masked_iban ?? 'No especificat' }}</span>
            </div>
            <div class="row">
                <span class="label">Titular del compte:</span>
                <span class="value">{{ $teacher->bank_titular ?? 'No especificat' }}</span>
            </div>
            <div class="row">
                <span class="label">Situació fiscal:</span>
                <span class="value">
                    @switch($teacher->fiscal_situation)
                        @case('autonom')
                            Autònom
                        @case('employee')
                            Treballador per compte aliè
                        @case('pensioner')
                            Pensionista
                        @default
                            Altres
                    @endswitch
                </span>
            </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">3. Autoritzacions</div>
        <div class="row">
            <span class="label">Consentiment de dades:</span>
            <span class="value">{{ $data_consent ? '✅ Acceptat' : '❌ No acceptat' }}</span>
        </div>
        <div class="row">
            <span class="label">Responsabilitat fiscal:</span>
            <span class="value">{{ $fiscal_responsibility ? '✅ Acceptada' : '❌ No acceptada' }}</span>
        </div>
    </div>

    @if($teacher->observacions)
    <div class="section">
        <div class="section-title">4. Observacions</div>
        <div class="row">
            <span class="value">{{ $teacher->observacions }}</span>
        </div>
    </div>
    @endif

    <div class="@if($data_consent && $fiscal_responsibility) 'approved' @else 'not-approved' @endif">
        @if($data_consent && $fiscal_responsibility)
            <strong>✅ PERFIL COMPLET I AUTORITZAT</strong><br>
            Aquest professor ha acceptat totes les autoritzacions necessàries.
        @else
            <strong>❌ PERFIL INCOMPLET</strong><br>
            Cal acceptar totes les autoritzacions necessàries.
        @endif
    </div>

    <div class="footer">
        Generat automàticament per Campus UPG - {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
