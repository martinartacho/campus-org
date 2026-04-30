<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>La Teva Connexió Ha Expirat | Universitat Popular de Granollers</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --accent-color: #3498db;
            --text-light: #ecf0f1;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .error-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            padding: 40px;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .error-icon {
            font-size: 4rem;
            color: var(--secondary-color);
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }
        
        .error-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .error-subtitle {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 500;
        }
        
        .narrative {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            text-align: left;
            border-left: 4px solid var(--accent-color);
        }
        
        .narrative h3 {
            color: var(--primary-color);
            font-family: 'Playfair Display', serif;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        
        .narrative p {
            color: #555;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        
        .btn-reload {
            background: var(--bg-gradient);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            display: inline-block;
            margin: 20px 10px;
        }
        
        .btn-reload:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .footer-info {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #666;
            font-size: 0.9rem;
        }
        
        .footer-info a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .footer-info a:hover {
            text-decoration: underline;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        @media (max-width: 768px) {
            .error-container {
                padding: 20px;
                margin: 10px;
            }
            
            .error-title {
                font-size: 2rem;
            }
            
            .narrative {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- Icona d'error -->
        <div class="error-icon">
            <i class="fas fa-clock"></i>
        </div>
        
        <!-- Títol principal -->
        <h1 class="error-title">La Teva Connexió Ha Expirat</h1>
        <p class="error-subtitle">Per la teva seguretat, la sessió ha finalitzat</p>
        
        <!-- Narrativa -->
        <div class="narrative">
            <h3>El Temps Espera a Ningú</h3>
            <p>
                Com les portes del campus que tan suaument al capvespre, la teva connexió ha completat el seu cicle. 
                Cada sessió és com una conversa: té un inici, un desenvolupament i una conclusió natural.
            </p>
            <p>
                Aquest no és un adeu, sinó una pausa necessària. El sistema ha guardat tot el teu treball, 
                protegit les teves dades i preparat el camí per a un nou començament fresc i segur.
            </p>
            <p>
                Ara tens l'oportunitat de prendre un respir, estirar les cames i tornar amb la ment clara. 
                El campus et espera amb els braços oberts, com sempre.
            </p>
        </div>
        
        <!-- Botons d'acció -->
        <div class="actions">
            <a href="{{ url('/') }}" class="btn-reload" onclick="clearAndReload(event)">
                <i class="fas fa-sync-alt me-2"></i>
                Netejar i Tornar a l'Inici
            </a>
            
            <a href="{{ route('login') }}" class="btn-reload btn-secondary">
                <i class="fas fa-sign-in-alt me-2"></i>
                Iniciar Sessió Novament
            </a>
        </div>
        
        <!-- Informació addicional -->
        <div class="footer-info">
            <p>
                <i class="fas fa-info-circle me-2"></i>
                Si aquest problema persisteix, contacta amb el 
                <a href="mailto:suport@upg.cat">servei tècnic</a> 
                o truca al 
                <a href="tel:+34938450123">938 450 123</a>
            </p>
            <p class="mt-2">
                <small>
                    <i class="fas fa-university me-1"></i>
                    Universitat Popular de Granollers © {{ date('Y') }} | 
                    <i class="fas fa-shield-alt me-1"></i>
                    La teva seguretat és la nostra prioritat
                </small>
            </p>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        function clearAndReload(event) {
            event.preventDefault();
            
            // Mostrar indicador de càrrega
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Netejant...';
            button.disabled = true;
            
            // Netejar tota la cache local
            if ('caches' in window) {
                caches.keys().then(function(names) {
                    names.forEach(function(name) {
                        caches.delete(name);
                    });
                });
            }
            
            // Netejar localStorage
            localStorage.clear();
            
            // Netejar sessionStorage
            sessionStorage.clear();
            
            // Esperar un moment i redirigir
            setTimeout(function() {
                window.location.href = button.href;
            }, 1500);
        }
        
        // Auto-redirecció després de 30 segons
        setTimeout(function() {
            const countdown = document.createElement('div');
            countdown.className = 'alert alert-info mt-3';
            countdown.innerHTML = '<i class="fas fa-info-circle me-2"></i>Seràs redirigit automàticament en <span id="countdown">30</span> segons...';
            document.querySelector('.actions').appendChild(countdown);
            
            let seconds = 30;
            const timer = setInterval(function() {
                seconds--;
                document.getElementById('countdown').textContent = seconds;
                
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.href = '{{ url('/') }}';
                }
            }, 1000);
        }, 5000);
    </script>
</body>
</html>
