<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perfil del Professor - {{ $teacher->full_name }}</title>
    <style>
        body {
            font-family: helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }
        h1 {
            font-size: 18px;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
        h2 {
            font-size: 14px;
            color: #34495e;
            border-bottom: 2px solid #3490dc;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
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
        .info-row {
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
        }
        .row {
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            color: #555;
            width: 150px;
            flex-shrink: 0;
        }
        .value {
            color: #333;
            flex: 1;
        }
        .declaration {
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-left: 3px solid #3490dc;
            border-radius: 3px;
        }
        .signature {
            margin-top: 30px;
            text-align: center;
        }
        .footer {
            font-size: 10px;
            color: #777;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            text-align: center;
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
    </style>
</head>
<body>

    <div class="header">
        <h1>Perfil del Professor</h1>
        <p>Data: {{ $date }}</p>
    </div>

    <div class="section">
        <h2>1. Dades Personals</h2>
        <div class="info-row">
            <span class="label">Nom complet:</span>
            <span class="value">{{ $teacher->first_name }} {{ $teacher->last_name }}</span>
        </div>
        <div class="info-row">
            <span class="label">DNI:</span>
            <span class="value">{{ $teacher->dni ?? 'No especificat' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Correu electrònic:</span>
            <span class="value">{{ $teacher->email }}</span>
        </div>
        <div class="info-row">
            <span class="label">Telèfon:</span>
            <span class="value">{{ $teacher->phone ?? 'No especificat' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Adreça:</span>
            <span class="value">{{ $teacher->address ?? 'No especificat' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Codi Postal:</span>
            <span class="value">{{ $teacher->postal_code ?? 'No especificat' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Ciutat:</span>
            <span class="value">{{ $teacher->city ?? 'No especificat' }}</span>
        </div>
    </div>

    <div class="section">
        <h2>2. Dades de Cobrament</h2>
        <div class="info-row">
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
            <div class="info-row">
                <span class="label">IBAN:</span>
                <span class="value">{{ $teacher->masked_iban ?? 'No especificat' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Titular del compte:</span>
                <span class="value">{{ $teacher->bank_titular ?? 'No especificat' }}</span>
            </div>
            <div class="info-row">
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
        <h2>3. DECLARACIONS I AUTORITZACIONS</h2>
        
        @if($fiscal_responsibility)
            <div class="declaration">
                <strong>DECLARACIÓ FISCAL:</strong> 
                Declara sota la meva responsabilitat que les dades facilitades són certes 
                i que es troba en alguna de les següents situacions fiscals:
                <ul style="margin-left: 20px; font-size: 11px; margin-top: 5px;">
                    <li>Soc autònom i presento declaracions trimestrals d'IVA</li>
                    <li>Soc pensionista i els meus ingressos estan exempts d'IRPF</li>
                    <li>Soc aturat i no tinc ingressos subjectes a retenció</li>
                    <li>Altres situacions exentes o amb retencions específiques</li>
                </ul>
            </div>
        @endif
        
        @if($data_consent)
            <div class="declaration">
                <strong>AUTORITZACIÓ TRACTAMENT DE DADES:</strong> 
                Autoritzo el tractament de les meves dades personals 
                amb finalitats fiscals i administratives, d'acord amb la normativa vigent de protecció de dades.
            </div>
        @endif
    </div>

    <div class="section">
        <h2>4. ESTAT DE LES AUTORITZACIONS</h2>
        <div class="info-row">
            <span class="label">Consentiment de dades:</span>
            <span class="value">{{ $data_consent ? '✅ Acceptat' : '❌ No acceptat' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Responsabilitat fiscal:</span>
            <span class="value">{{ $fiscal_responsibility ? '✅ Acceptada' : '❌ No acceptada' }}</span>
        </div>
    </div>

    @if($teacher->observacions)
    <div class="section">
        <h2>5. Observacions</h2>
        <div class="info-row">
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

    <div class="signature">
        <p>____________</p>
        <p><strong>{{ $teacher->first_name }} {{ $teacher->last_name }}</strong></p>
        <p>DNI: {{ $teacher->dni ?? 'No especificat' }}</p>
        <p style="margin-top: 10px; font-size: 11px; color: #666;">
            Document generat electrònicament el {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>

    <div class="footer">
        <p><strong>PROTECCIÓ DE DADES - RGPD</strong></p>
        <p>A la UPG tractem la informació que ens faciliteu exclusivament per oferir el servei sol·licitat. Les dades proporcionades es conservaran mentre es mantingui la relació formativa, o durant els anys necessaris per complir amb les obligacions legals.</p>
        <p>Generat automàticament per Campus UPG - {{ now()->format('d/m/Y H:i') }}</p>
    </div>

</body>
</html>
