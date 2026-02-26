<!DOCTYPE html>
<html>
<head>
    <title>{{ __('Dades del professorat de la UPG') }}</title>
</head>
<body>
    <h1>{{ __('Dades del professorat de la UPG') }}</h1>
    <div>
        <p>
           {{ __('Hola') }} {{ $teacher->teacherProfile ? $teacher->teacherProfile->full_name : ($teacher->name ?? 'Docent') }}

        </p>

        @if ($purpose === 'payments')
           
            <p>
                {{ __('Et donem la benvinguda al grup de professorat de la Universitat Popular de Granollers (UPG) i agraïm  la teva participació en la confinaça que serà de mútua satisfacció.') }}
            </p>
                @if(!empty($additionalData))
                    <p>{{ __('Curs') }}: {{ $additionalData['course_title'] ?? '' }}</p>
                @endif
            <p>
                {{ __('Per tal de gestionar correctament la formació que imparteixes aquest curs, et donem seguidament l´enllaç al formulari per a completar les teves dades.') }}
                Consideracions:
                {{ __('Rebràs un missatge com aquest si imparteix més d´un curs.') }}
                {{ __('A cada curs impartit, pots triar diferents modalitats de cobrament, tria la que consideris.') }}
                {{ __('Alguna observació ...') }}
            </p>

            <p>
                <a href="{{ route('teacher.access.form', [
                    'token' => $token->token,
                    'purpose' => 'payments',
                    'courseCode' => $courseCode
                ]) }}">
                    {{ __('Formulari dades professorat UPG') }} 
                </a>
            </p>
                
            <p>
                <span>{{ __('o copia i enganxa aquest enllaç al teu navegador:') }}</span>
                <code>
                    {{ config('app.url') }}/teacher/access/{{ $token->token }}/payments/{{ $courseCode }}
                </code>
            </p>
        @else
            
            <p>
                {{ __('Benvingut docent de la Universitat Popular de Granollers - UPG') }}
            </p>
            <p>
                {{ __('Per tal de gestionar correctament la formació que imparteixes aquest curs, et donem seguidament l´enllaç al formulari per a completar les teves dades.') }}

                Consideracions:
                {{ __('Rebràs un missatge com aquest si imparteix més d´un curs.') }}
                {{ __('A cada curs impartit, pots triar diferents modalitats de cobrament, tria la que consideris.') }}
                {{ __('Alguna observació ...') }}

            </p>

            <p>
                <a href="{{ route('teacher.access.form', [
                    'token' => $token->token,
                    'purpose' => 'consent',
                    'courseCode' => $courseCode
                ]) }}">
                    {{ __('Formulari dades professorat UPG') }}
                </a>
            </p>
            <p>
                <span>{{ __('o copia i enganxa aquest enllaç al teu navegador:') }}</span>
                <code>
                    {{ config('app.url') }}/teacher/access/{{ $token->token }}/consent/{{ $courseCode }}
                </code>
            </p>
        @endif

        <p>
            {{ __('Aquest enllaç caduca en 7 dies.') }}
        </p>
        <p>
            @if(!empty($additionalData))
                <strong>{{  __( 'Equip de tresoreria') }}</strong> per <em>{{ $additionalData['autoria'] ?? '' }}</em>
            @else
                {{ __('Missatge enviat per:') }} {{  __( 'Equip de tresoreria') }}
            @endif
        </p>

        <p>
           {{  __( 'Associació per l\'impuls d\'Estudis Populars (AIEP) - CIF G-66314998 -Carrer Mare de Deu de Montserrat, edifici Roca Umbert, 36 - 08401 Granollers') }} 
        </p>

        
        <p>
            {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
