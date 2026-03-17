<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document de Pagament - {{ $teacher->first_name }} {{ $teacher->last_name }}</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            line-height: 1.6; 
            color: #333;
            font-size: 12px;
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #333; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
        }
        .header h1 { 
            font-size: 18px; 
            color: #2c3e50; 
            margin-bottom: 5px;
        }
        .header p { margin: 2px 0; }
        .section { 
            margin-bottom: 15px; 
            page-break-inside: avoid;
        }
        .section-title { 
            background-color: #f8f9fa; 
            padding: 6px 10px; 
            font-weight: bold; 
            border-left: 4px solid #3490dc;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .info-grid { 
            width: 100%;
        }
        .info-item { 
            float: left; 
            width: 48%; 
            margin-right: 2%;
            margin-bottom: 8px; 
            min-width: 200px;
        }
        .info-grid::after {
            content: "";
            display: table;
            clear: both;
        }
        .label { 
            font-weight: bold; 
            color: #555; 
            font-size: 11px;
            margin-bottom: 2px;
        }
        .value { 
            padding: 3px 0; 
            font-size: 12px;
            border-bottom: 1px dotted #eee;
        }
        .signature-line { 
            margin-top: 40px; 
            text-align: right;
            padding-top: 20px;
            border-top: 1px solid #ccc;
        }
        .timestamp { 
            font-size: 10px; 
            color: #666; 
            text-align: center; 
            margin-top: 20px;
        }
        .checksum { 
            font-family: monospace; 
            font-size: 9px; 
            color: #888; 
            word-break: break-all; 
            text-align: center;
            margin-top: 5px;
        }
        .footer { 
            position: fixed; 
            bottom: 10px; 
            width: 100%; 
            text-align: center; 
            font-size: 9px; 
            color: #999;
        }
        .payment-option { 
            padding: 8px; 
            margin: 8px 0; 
            border-radius: 4px;
            font-size: 12px;
        }
        .payment-option.own { 
            background-color: #d1ecf1; 
            border: 1px solid #bee5eb; 
        }
        .payment-option.ceded { 
            background-color: #d4edda; 
            border: 1px solid #c3e6cb; 
        }
        .payment-option.waived { 
            background-color: #fff3cd; 
            border: 1px solid #ffeaa7; 
        }
        .course-info {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }
        .course-info .label { font-size: 10px; }
        .declaration {
            background-color: #e8f4fd;
            padding: 8px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #b6d4fe;
            font-size: 11px;
        }
        @page { margin: 50px 25px; }
    </style>
</head>
<body>
    <div class="header">
        @if(isset($isFinalConsent) && $isFinalConsent)
        <div style="background-color: #28a745; color: white; padding: 8px; margin-bottom: 10px; text-align: center; font-weight: bold;">
            🎯 CONSENTIMENT FINAL COMPLET - PROCÉS FINALITZAT
        </div>
        @endif
        <h1>DOCUMENT DE DADES DE PAGAMENT</h1>
        <p>Universitat Popular de Granollers - Tresoreria</p>
        <p>Temporada: {{ $season->name ?? 'N/A' }} </p>
        <p>ID: PAY-{{ $teacher->id }}-{{ $course->id ?? 'N/A' }}-{{ $acceptedAt->format('YmdHis') }}</p>
    </div>

    <!-- Informació del Professor -->
    <div class="section">
        <div class="section-title">1. INFORMACIÓ PROFESSOR/A</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Nom complet:{{ $teacher->first_name }} {{ $teacher->last_name }}</div>
            </div>
            <div class="info-item">
                <span class="label">Email:</span> <span class="value">{{ $teacher->email }}</span>
            </div>
            <div class="info-item">
                <div class="label">Telèfon:</div>
                <div class="value">{{ $teacher->phone ?? 'No especificat' }}</div>
            </div>
            <div class="info-item">
                <div class="label">DNI/NIF:</div>
                <div class="value">{{ $teacher->dni ?? 'No especificat' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Codi Professor:</div>
                <div class="value">{{ $teacher->teacher_code ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Data registre:</div>
                <div class="value">{{ $acceptedAt->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>
    </div>

    <!-- Informació del Curs -->
    <div class="section">
        <div class="section-title">2. ACTIVITAT FORMATIVA</div>
        <div class="course-info">
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">ID Curs:</div>
                    <div class="value">{{ $course->id ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Títol del curs:</div>
                    <div class="value">{{ $course->title ?? 'No assignat' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Temporada:</div>
                    <div class="value">[{{ $season->slug ?? 'N/A' }}] {{ $season->name ?? 'N/A' }}</div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Opció de Pagament -->
    <div class="section">
        <div class="section-title">3. OPCIÓ DE PAGAMENT</div>
        <div class="payment-option {{ $paymentOption }}">
            <strong>{{ $paymentOptionLabel }}</strong>
        </div>
        
        @if($paymentOption === 'waived_fee')
        <div class="declaration">
            <strong>⚠️ RENÚNCIA VOLUNTÀRIA:</strong> El/la professor/a renuncia voluntàriament a tots els drets de cobrament 
            i a les obligacions fiscals derivades per a aquest curs específic.
        </div>
        @endif
    </div>

    <!-- Dades Fiscals i Bancàries (només si no és waived_fee) -->
    @if(!in_array($paymentOption, ['waived_fee']))
    <div class="section">
        <div class="section-title">4. DADES FISCALS I BANCÀRIES</div>
        <div class="info-grid">
            @if($fiscalId)
            <div class="info-item">
                <div class="label">Identificació fiscal:</div>
                <div class="value">{{ $fiscalId }}</div>
            </div>
            @endif
            
            @if($address)
            <div class="info-item">
                <div class="label">Adreça fiscal:</div>
                <div class="value">{{ $address }}</div>
            </div>
            @endif
            
            @if($postalCode)
            <div class="info-item">
                <div class="label">Codi postal:</div>
                <div class="value">{{ $postalCode }}</div>
            </div>
            @endif
            
            @if($city)
            <div class="info-item">
                <div class="label">Població i província:</div>
                <div class="value">{{ $city }}</div>
            </div>
            @endif
            
            @if($iban)
            <div class="info-item">
                <div class="label">IBAN:</div>
                <div class="value">{{ $teacher->masked_iban }}</div>
            </div>
            @endif
            
            
            @if($fiscalSituation)
            <div class="info-item">
                <div class="label">Situació fiscal declarada:</div>
                <div class="value">{{ $fiscalSituation }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Declaracions -->
    <div class="section">
        <div class="section-title">5. DECLARACIONS I AUTORITZACIONS</div>
        <div class="declaration">
            @if(isset($declaracioFiscal) && $declaracioFiscal)
            <p><strong>✅ Declaració fiscal:</strong> Declara sota la meva responsabilitat que les dades facilitades són certes 
            i que es troba en alguna de les següents situacions fiscals:</p>
            <ul style="margin-left: 20px; font-size: 11px;">
                <li>Soc autònom i presento declaracions trimestrals d'IVA</li>
                <li>Soc pensionista i els meus ingressos estan exempts d'IRPF</li>
                <li>Soc aturat i no tinc ingressos subjectes a retenció</li>
                <li>Altres situacions exentes o amb retencions específiques</li>
            </ul>
            
            @endif
            
            @if(isset($autoritzacioDades) && $autoritzacioDades)
            <p><strong>✅ Autorització tractament de dades:</strong> Autoritzo el tractament de les meves dades personals 
            amb finalitats fiscals i administratives, d'acord amb la normativa vigent de protecció de dades.</p>
            @else
            <p><strong>❌ Autorització tractament de dades:</strong> No s'ha registrat l'autorització de tractament de dades del beneficiari.</p>
            @endif
        </div>
    </div>

    <!-- Signatura i Validació -->
    <div class="signature-line">
        <p>_________________________________________</p>
        <p>Signatura digital del professor/a</p>
        <p>{{ $teacher->first_name }} {{ $teacher->last_name }}</p>
        <p>DNI: {{ $teacher->dni ?? 'No especificat' }}</p>
    </div>
    
    <div class="timestamp">
        <p>Document vàlid exclusivament per al curs: {{ $course->title ?? 'N/A' }}</p>
        <p>Generat el: {{ $acceptedAt->format('d/m/Y H:i:s') }}</p>
    </div>
    
    @if($checksum)
    <div class="checksum">
        Checksum de validació: {{ $checksum }}
    </div>
    @endif

    <div class="footer">
        Document generat automàticament • Universitat Popular de Granollers • 
        Curs ID: {{ $course->id ?? 'N/A' }} • 
        Professor ID: {{ $teacher->id }}
    </div>
</body>
</html>