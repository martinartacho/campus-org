<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Consentiment RGPD {{ $season }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: normal;
        }
        p {
            margin-bottom: 10px;
        }
        ul {
            margin: 0;
            padding-left: 16px;
        }
        .box {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h1>Consentiment RGPD - Professorat – Temporada {{ $season }}</h1>
<h2>versió treasury</h2>

<p>
Jo,
<strong>{{ $teacher->last_name }} {{ $teacher->first_name }}</strong>,
amb correu electrònic
<strong>{{ $email }}</strong>,
declaro que he llegit i accepto el tractament de les meves dades personals
amb finalitats administratives i econòmiques.
</p>

<div class="box">
    <p><strong>Dades declarades:</strong></p>
    <ul>
        <li>Nom i cognoms: {{ $teacher->last_name }} {{ $teacher->first_name }}</li>
        <li>Identificador: {{ $teacher->dni ?? '—' }}</li>
        <li>Correu electrònic: {{ $email }}</li>
        <li>Codi professor: {{ $teacher->teacher_code }}</li>
    </ul>
</div>

<p style="margin-top:20px;">
Consentiment acceptat el dia
<strong>{{ $acceptedAt->format('d/m/Y H:i') }}</strong>
</p>

<p style="margin-top:40px;">
Signatura digital registrada pel sistema.
</p>

</body>
</html>
