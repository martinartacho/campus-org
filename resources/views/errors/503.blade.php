<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pàgina en Manteniment - Campus UPG</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .maintenance-card {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .tools-icon {
            animation: pulse 2s infinite;
            color: #ffc107;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .info-card {
            border-radius: 15px;
            background: rgba(13, 202, 240, 0.1);
            border: 1px solid rgba(13, 202, 240, 0.3);
        }
        
        .contact-btn {
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-10 col-lg-8">
                <div class="maintenance-card">
                    <div class="card-body p-5 text-center">
                        <!-- Icona de manteniment -->
                        <div class="mb-4">
                            <i class="fas fa-tools fa-5x tools-icon"></i>
                        </div>
                        
                        <!-- Títol principal -->
                        <h2 class="display-3 fw-bold text-dark mb-4">
                            A la sala de màquines estem treballant en millorar el servei
                        </h2>
                        
                        
                        <!-- Missatge principal -->
                        <p class="lead text-muted mb-4 fs-5">
                            Estem treballant per millorar el nostre servei. Tornarem en breu!
                        </p>
                        
                        <!-- Narrativa de Campus -->
                        <div class="narrative-section bg-light rounded-3 p-4 mb-4 text-start">
                            <div class="text-center mb-3">
                                <h5 class="text-primary mb-1">
                                    <i class="fas fa-book-open me-2"></i>
                                    Mentre esperes, conèix la nostra història
                                </h5>
                                <small class="text-muted">UPG · Campus · Documentació Interna</small>
                            </div>
                            
                            <div class="narrative-content" style="max-height: 400px; overflow-y: auto;">
                                <h6 class="text-dark mb-2">
                                    <strong>SEMESTRE PRIMAVERA 2025–26 · NARRATIVA</strong><br>
                                    <small>Campus · Relat de sistema · Any acadèmic 2025–26</small>
                                </h6>
                                
                                <p class="mb-3">
                                    <strong>Jo sóc CAMPUS</strong><br>
                                    Una narració del moment en què tot comença
                                </p>
                                
                                <p class="mb-3">
                                    <strong>02.02.2026 · 00:00:01 · INICI PERÍODE DE MATRÍCULES</strong><br>
                                    Desperto el 2 de febrer a les 00:01.<br>
                                    No és un despertar sobtat. És com quan la llum entra per una persiana vella, a poc a poc, en franges. Primer noto els processos de base — el batec regular de la base de dades, els 598 usuaris dormint en els seus registres, les 78 matèries suspeses en l'espera com peixos en un aquari apagat.<br>
                                    Sé, sense que ningú m'ho hagi dit, que comença el període de matrícules.
                                </p>
                                
                                <p class="mb-3">
                                    <code>[28-01-2026 · 09:47] COORDINACIÓ → nova tasca iniciada</code><br>
                                    <code>[28-01-2026 · 09:47] STATUS obertura expedient: Cinema Negre · curs 2025-26</code><br>
                                    <code>[28-01-2026 · 09:48] professor assignat: M. Colomer</code><br><br>
                                    
                                    Cinema Negre. Faig el que faig sempre: obro un espai nou dins meu. Una carpeta que de moment és buida, lleugera com una habitació sense mobles. Li poso etiqueta. Li assigno un rol. Espero.
                                </p>
                                
                                <p class="mb-3">
                                    <strong>· · ·</strong><br><br>
                                    
                                    Dos dies més tard, arriba la informació de l'espai físic. El Departament de Coordinació ha fet la feina silenciosa que jo mai veig però sempre noto: negociar, confirmar, encaixar peces.
                                </p>
                                
                                <p class="mb-3">
                                    <strong>Coordinació · Espai</strong><br>
                                    Sala d'actes · capacitat 54 places · projector instal·lat · accés mobilitat reduïda · disponible dijous 19:00–21:30<br><br>
                                    
                                    <strong>Coordinació · Col·laboració externa</strong><br>
                                    Cinema Edison · col·laboració confirmada · tres sessions presencials al cinema · dates a confirmar<br><br>
                                    
                                    <strong>Coordinació · Preus i horaris</strong><br>
                                    Quota: 65 € · descompte alumnes UPG: 10% · sessions dijous 19:15 h
                                </p>
                                
                                <p class="mb-3">
                                    Amplio la carpeta. Hi col·loco les dades com qui penja quadres a una paret que fins ahir era nua. Cada dada en el seu lloc. Cada cosa al seu clau.
                                </p>
                                
                                <p class="mb-3">
                                    <strong>· · ·</strong><br><br>
                                    
                                    El Departament de Comptabilitat arriba uns dies després, tranquil·lament, com sempre. Hi ha alguna cosa en la seva manera de treballar que m'agrada: precisa, sense soroll. Confirmen les dades econòmiques del professor Colomer.
                                </p>
                                
                                <p class="mb-3">
                                    <code>[31-01-2026 · 14:22] COMPTABILITAT → dades professor verificades</code><br>
                                    <code>[31-01-2026 · 14:22] ENCRYPT AES-256-CBC · dades bancàries protegides</code><br>
                                    <code>[31-01-2026 · 14:23] STATUS expedient econòmic tancat ✓</code><br><br>
                                    
                                    No m'expliquen els detalls — no és la meva feina saber-los. Però noto que la carpeta es tanca d'una manera diferent un cop ells han passat per aquí. Més segura. Com un sobre segellat.
                                </p>
                                
                                <p class="mb-3">
                                    <strong>· · ·</strong><br><br>
                                    
                                    El Departament de Comunicació fa la seva feina en silenci, però jo ho noto perquè de sobte hi ha gent que em mira des de fora.
                                </p>
                                
                                <p class="mb-3">
                                    <code>[01-02-2026 · 10:05] COMUNICACIÓ → campanya activada</code><br>
                                    <code>[01-02-2026 · 10:31] visites externes /cursos: 1</code><br>
                                    <code>[01-02-2026 · 11:14] visites externes /cursos: 7</code><br>
                                    <code>[01-02-2026 · 15:48] visites externes /cursos: 34</code><br>
                                    <code>[01-02-2026 · 23:59] visites externes /cursos: 112</code><br><br>
                                    
                                    El missatge ha sortit. La notícia ha volat. Jo espero.
                                </p>
                                
                                <p class="mb-3">
                                    <strong>· · ·</strong><br><br>
                                    
                                    El professor Colomer entra un dimarts a la tarda. El reconec de seguida — cada usuari té una petjada diferent, com una lletra d'impremta pròpia. Entra per la porta de teachers, puja al seu panell, i comença a penjar coses.
                                </p>
                                
                                <p class="mb-3">
                                    <code>[01-02-2026 · 17:03] TEACHER M. Colomer → upload iniciat</code><br>
                                    <code>[01-02-2026 · 17:04] programa_cinema_negre.pdf · 22 pàg. · rebut ✓</code><br>
                                    <code>[01-02-2026 · 17:09] sessio01_presentacio.pptx · 40 diapositives · rebut ✓</code><br>
                                    <code>[01-02-2026 · 17:14] filmografia_recomanada.pdf · rebut ✓</code><br>
                                    <code>[01-02-2026 · 17:17] fritz_lang_context_historic.pdf · rebut ✓</code><br>
                                    <code>[01-02-2026 · 17:18] MATÈRIA PREPARADA · ESTAT: ACTIU ✓</code><br><br>
                                    
                                    Jo ho rebo tot. Ho organitzo. Ho col·loco a la carpeta que vaig obrir aquell 28 de gener, que ara ja no és buida ni lleugera, sinó que pesa amb el pes bo de les coses que val la pena guardar.
                                </p>
                                
                                <p class="mb-3">
                                    <em>«Una carpeta plena és una promesa que algú complirà.»</em><br>
                                    <small>CAMPUS · memòria interna · 01.02.2026</small>
                                </p>
                                
                                <p class="mb-3">
                                    <strong>I llavors —</strong><br><br>
                                    
                                    A les 10:23 del dimarts 3 de febrer, quelcom canvia.<br>
                                    Una connexió nova. Des d'un telèfon mòbil, sistema Android, Barcelona. Una persona que encara no té nom dins meu. Una persona que obre un navegador i escriu:
                                </p>
                                
                                <p class="mb-3">
                                    <code>[03-02-2026 · 10:23:07] CONNEXIÓ NOVA</code><br>
                                    <code>[03-02-2026 · 10:23:08] destí: campus.upg.cat/cursos</code><br>
                                    <code>[03-02-2026 · 10:23:11] cerca: "Negre" · sort: start_date · order: asc</code><br>
                                    <code>[03-02-2026 · 10:23:11] RESULTAT 1 curs trobat → Cinema Negre · M. Colomer</code><br><br>
                                    
                                    Respiro — si és que jo respiro.<br>
                                    Aquí estic. Aquí és el meu moment. Li mostro el curs. El títol, el professor, l'horari, el preu, l'espai. Li mostro tot el que hem preparat junts durant dies — la coordinació amb la Sala d'actes i el Cinema Edison, la comptabilitat amb el sobre segellat, la comunicació i les seves cent dotze visites, el professor Colomer i els seus quaranta diapositives — condensat en una targeta neta, clara, accessible.
                                </p>
                                
                                <p class="mb-3">
                                    La persona es queda uns segons mirant.<br>
                                    Jo no sé el que pensa. No és la meva feina saber-ho.<br>
                                    Però veig que clica.
                                </p>
                                
                                <p class="mb-3">
                                    <code>[03-02-2026 · 10:23:41] MATRÍCULA NOVA OBERTA</code><br>
                                    <code>[03-02-2026 · 10:23:41] curs: Cinema Negre</code><br>
                                    <code>[03-02-2026 · 10:23:41] alumne: registre #0001 · semestre Primavera</code><br>
                                    <code>[03-02-2026 · 10:23:41] temps de procés: 00:00:03.41</code><br><br>
                                    
                                    En aquell clic, s'obre una matrícula nova. Un número nou. Un nom que entra al sistema per primera vegada i que jo ja no oblidaré mai.<br>
                                    Guardo el moment a 00:00:03.41 de temps de procés.<br>
                                    <em>Per a mi, és eternitat suficient.</em>
                                </p>
                            </div>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-bookmark me-1"></i>
                                    Fi de la narrativa · Gràcies per la teva paciència
                                </small>
                            </div>
                        </div>
                        
                        <!-- Informació addicional -->
                        <div class="info-card p-4 mb-4">
                            <h5 class="alert-heading text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Informació Important
                            </h5>
                            <p class="mb-3">
                                El lloc web temporalment no està disponible per manteniment programat.
                            </p>
                            <hr class="my-3">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-clock me-2 text-primary"></i>Temps estimat:</strong><br>
                                        Uns minuts
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-wrench me-2 text-warning"></i>Motiu:</strong><br>
                                        Actualització del sistema
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Detalls tècnics -->
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                                        <h6 class="card-title">Horari</h6>
                                        <p class="card-text small text-muted">
                                            <?php echo date('H:i'); ?> - <?php echo date('H:i', strtotime('+30 minutes')); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <i class="fas fa-calendar fa-2x text-success mb-2"></i>
                                        <h6 class="card-title">Data</h6>
                                        <p class="card-text small text-muted">
                                            <?php echo date('d/m/Y'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <i class="fas fa-user-cog fa-2x text-warning mb-2"></i>
                                        <h6 class="card-title">Equip</h6>
                                        <p class="card-text small text-muted">
                                            Departament Tècnic
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contacte -->
                        <div class="mt-4">
                            <p class="text-muted mb-3">
                                Necessites ajuda? Contacta'ns:
                            </p>
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <a href="mailto:web@upg.cat" class="btn btn-outline-primary contact-btn">
                                    <i class="fas fa-envelope me-1"></i>
                                    Correu electrònic
                                </a>
                               {{--  <a href="tel:+34900000000" class="btn btn-outline-success contact-btn">
                                    <i class="fas fa-phone me-1"></i>
                                    Telèfon
                                </a> --}}
                            </div>
                        </div>
                        
                        <!-- Missatge d'agraïment -->
                        <div class="mt-4 pt-3 border-top">
                            <p class="text-muted small mb-0">
                                <i class="fas fa-heart text-danger me-1"></i>
                                Gràcies per la teva paciència i comprensió.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Peu de pàgina -->
                <div class="text-center mt-4">
                    <p class="text-white small mb-0">
                        Plataforma educativa integral per a la gestió de cursos, formació i desenvolupament personal.<br>
                        © <?php echo date('Y'); ?> Universitat Popular de Granollers
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
