<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('site.Event Calendar') }} - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8fafc;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .filters {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .btn:hover {
            background-color: #2563eb;
        }
        
        #calendar {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        /* Ajustes específicos para FullCalendar */
        .fc .fc-toolbar-title {
            font-size: 1.5em;
            font-weight: 600;
        }
        
        .fc-event {
            cursor: pointer;
        }
        
        .event-with-questions {
            border-left: 3px solid #f6c23e;
        }
        
        .question-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #f6c23e;
            color: #000;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .modal-header {
            background: linear-gradient(90deg, #4e73df 0%, #2a3e9d 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }
        
        .badge-event-detail {
            font-size: 0.85em;
            margin-right: 5px;
        }
        
        .response-form {
            background-color: #f8f9fc;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .visibility-info {
            background-color: #f8f9fc;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #4e73df;
        }
        
        /* Estilos para opciones de radio button mejoradas */
        .radio-options-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        
        .radio-option {
            position: relative;
        }
        
        .radio-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .radio-option label {
            display: inline-block;
            padding: 8px 16px;
            background-color: #f8f9fc;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .radio-option input[type="radio"]:checked + label {
            background-color: #4e73df;
            color: white;
            border-color: #4e73df;
        }
        
        .radio-option label:hover {
            background-color: #e9ecef;
        }
        
        /* Estilos para checkboxes */
        .checkbox-options-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 8px;
        }
        
        .checkbox-option {
            display: flex;
            align-items: center;
        }
        
        .checkbox-option input[type="checkbox"] {
            margin-right: 8px;
        }
        
        .question-title {
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }

            .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 1.5rem; font-weight: 600;">{{ __('site.Event Calendar') }}</h1>
            <div>
                @auth
                    <a href="{{ url('dashboard') }}" class="btn">{{ __('site.Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn">{{ __('site.Login') }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn" style="background-color: #6b7280;">{{ __('site.Register') }}</a>
                    @endif
                @endauth
            </div>
        </div>
        
        <div class="filters">
            <label for="event_type_filter">{{ __('site.Filter by Type') }}:</label>
            <select id="event_type_filter" class="event-type-filter">
                <option value="">{{ __('site.All Types') }}</option>
                @foreach($eventTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div id="calendar"></div>
    </div>

    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">{{ __('site.Event Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h4 id="eventTitle">{{ __('site.Title') }}</h4>
                            <div class="d-flex flex-wrap mb-2">
                                <span class="badge bg-primary badge-event-detail" id="eventDate">
                                <i class="fas fa-calendar me-1"></i> <span id="eventDateText">{{ __('site.Date') }}</span>
                                </span>
                                <span class="badge bg-info badge-event-detail" id="eventTime">
                                <i class="fas fa-clock me-1"></i> <span id="eventTimeText">{{ __('site.Time') }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <h5>{{ __('site.Description') }}</h5>
                            <p id="eventDescription">{{ __('site.No description') }}</p>
                        </div>
                    </div>

                    <!-- Información de Visibilidad como frase -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="visibility-info">
                                <i class="fas fa-eye me-1"></i>
                                <span id="visibilityText">{{ __('site.Visible from') }} ...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Respuestas -->
                    <div class="row" id="responseSection">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5>{{ __('site.Answer Questions') }}</h5>
                                <span class="badge bg-info" id="responseStatus">{{ __('site.Available') }}</span>
                            </div>
                            <div class="response-form">
                                <form id="responseForm">
                                    <!-- Los campos de respuesta se insertarán aquí dinámicamente -->
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success" id="submitResponseBtn">
                                            <i class="fas fa-paper-plane me-1"></i> {{ __('site.Submit Answers') }}
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" id="cancelResponseBtn">
                                            <i class="fas fa-times me-1"></i> {{ __('site.Cancel') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('site.Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/{{ app()->getLocale() }}.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var eventTypeFilter = document.getElementById('event_type_filter');
            var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            var currentEventDetails = null; // Variable global para almacenar los detalles del evento actual
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: function(fetchInfo, successCallback, failureCallback) {
                    var url = '{{ route("calendar.events") }}?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr;
                    
                    if (eventTypeFilter.value) {
                        url += '&event_type_id=' + eventTypeFilter.value;
                    }
                    
                    fetch(url)
                        .then(response => response.json())
                        .then(events => {
                            // Procesar eventos para añadir información sobre preguntas
                            const processedEvents = events.map(event => {
                                return {
                                    ...event,
                                    extendedProps: {
                                        ...event.extendedProps,
                                        has_questions: event.extendedProps.has_questions || false
                                    }
                                };
                            });
                            successCallback(processedEvents);
                        })
                        .catch(error => failureCallback(error));
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                eventDidMount: function(info) {
                    // Resaltar eventos con preguntas
                    if (info.event.extendedProps.has_questions) {
                        info.el.classList.add('event-with-questions');
                        
                        // Agregar badge de preguntas
                        const badge = document.createElement('div');
                        badge.classList.add('question-badge');
                        badge.title = '{{ __("site.This event has questions") }}';
                        badge.innerHTML = '<i class="fas fa-question"></i>';
                        
                        info.el.appendChild(badge);
                    }
                },

                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    
                    // Obtener detalles completos del evento
                    const eventId = info.event.id;
                    const url = `{{ route('calendar.event.details', ['event' => ':eventId']) }}`.replace(':eventId', eventId);
                    
                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error al cargar los detalles del evento: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(eventDetails => {
                            currentEventDetails = eventDetails; // Almacenar detalles del evento
                            
                            // Cargar datos del evento en el modal
                            const event = info.event;
                            const extendedProps = eventDetails.extendedProps || {}; // Definir extendedProps aquí
                            
                            // Información básica
                            document.getElementById('eventTitle').textContent = event.title;
                            document.getElementById('eventDescription').textContent = extendedProps.description || '{{ __("site.No description") }}';
                            
                            // Fechas y horas
                            const startDate = event.start ? new Date(event.start) : null;
                            const endDate = event.end ? new Date(event.end) : null;
                            
                            if (event.allDay) {
                                document.getElementById('eventDateText').textContent = startDate.toLocaleDateString('es-ES');
                                document.getElementById('eventTimeText').textContent = '{{ __("site.All day") }}';
                            } else {
                                document.getElementById('eventDateText').textContent = startDate.toLocaleDateString('es-ES');
                                
                                
                                // Si es el mismo día, mostrar solo la hora para el final
                                if (startDate.toDateString() === endDate.toDateString()) {
                                    document.getElementById('eventTimeText').textContent = 
                                        startDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) + 
                                        ' - ' + 
                                        endDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                                } else {
                                    document.getElementById('eventTimeText').textContent = 
                                        startDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) + 
                                        ' (' + startDate.toLocaleDateString('es-ES') + ') - ' + 
                                        endDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) + 
                                        ' (' + endDate.toLocaleDateString('es-ES') + ')';
                                }
                            }
                            
                            // Información de visibilidad como frase
                            const visibilityRow = document.querySelector('.visibility-info').parentNode.parentNode;
                            if (eventDetails.end_visible) {
                                const startVisible = eventDetails.start_visible ? 
                                    new Date(eventDetails.start_visible).toLocaleDateString('es-ES') : '{{ __("site.Not set") }}';
                                const endVisible = new Date(eventDetails.end_visible).toLocaleDateString('es-ES');

                                document.getElementById('visibilityText').textContent = 
                                    `{{ __("site.Visible from") }} ${startVisible} {{ __("site.until") }} ${endVisible}`;
                                visibilityRow.style.display = 'block';
                            } else {
                                visibilityRow.style.display = 'none';
                            }
                            
                            // Respuestas
                            const responseSection = document.getElementById('responseSection');
                            const responseForm = document.getElementById('responseForm');
                            const responseStatus = document.getElementById('responseStatus');
                            
                            // Verificar condiciones para aceptar respuestas
                            const now = new Date();
                            const startVisibleDate = eventDetails.start_visible ? new Date(eventDetails.start_visible) : null;
                            const endVisibleDate = eventDetails.end_visible ? new Date(eventDetails.end_visible) : null;
                            
                            const isVisible = (!startVisibleDate || now >= startVisibleDate) && (!endVisibleDate || now <= endVisibleDate);
                            const hasSpace = !eventDetails.max_users || (extendedProps.registered_users || 0) < eventDetails.max_users;
                            const hasQuestions = extendedProps.questions && extendedProps.questions.length > 0;
                            
                            if (isVisible && hasSpace && hasQuestions) {
                                responseSection.style.display = 'block';
                                responseStatus.textContent = '{{ __("site.Available for answers") }}';
                                responseStatus.className = 'badge bg-success';
                                
                                // Limpiar formulario anterior
                                responseForm.innerHTML = '';
                                
                                // Agregar campos para respuestas
                                extendedProps.questions.forEach((question, index) => {
                                    const formGroup = document.createElement('div');
                                    formGroup.className = 'mb-4 p-3 border rounded';
                                    
                                    // Título de la pregunta
                                    const questionTitle = document.createElement('div');
                                    questionTitle.className = `question-title ${question.required ? 'required-field' : ''}`;
                                    questionTitle.textContent = `${index + 1}. ${question.question}`;
                                    formGroup.appendChild(questionTitle);
                                    
                                    if (question.type === 'text') {
                                        // Campo de texto
                                        const textarea = document.createElement('textarea');
                                        textarea.className = 'form-control mt-2';
                                        textarea.name = `answer_${question.id}`;
                                        textarea.rows = 3;
                                        textarea.placeholder = '{{ __("site.Write your answer here...") }}';
                                        if (question.required) textarea.required = true;
                                        if (question.user_response) textarea.value = question.user_response;
                                        formGroup.appendChild(textarea);
                                    } else if (question.type === 'single') {
                                        // Opciones únicas (radio buttons)
                                        const optionsContainer = document.createElement('div');
                                        optionsContainer.className = 'radio-options-container mt-2';
                                        
                                        if (question.options) {
                                            // Convertir options a array si es necesario
                                            let optionsArray = [];
                                            if (Array.isArray(question.options)) {
                                                optionsArray = question.options;
                                            } else if (typeof question.options === 'string') {
                                                optionsArray = question.options.split(',');
                                            }
                                            
                                            optionsArray.forEach((option, optIndex) => {
                                                const optionValue = option.trim ? option.trim() : option;
                                                const isChecked = (question.user_response === optionValue);
                                                
                                                const optionDiv = document.createElement('div');
                                                optionDiv.className = 'radio-option';
                                                
                                                
                                                const radioInput = document.createElement('input');
                                                radioInput.type = 'radio';
                                                radioInput.name = `answer_${question.id}`;
                                                radioInput.value = optionValue;
                                                radioInput.id = `question_${question.id}_option_${optIndex}`;
                                                if (question.required) radioInput.required = true;
                                                if (isChecked) radioInput.checked = true;
                                                
                                                const radioLabel = document.createElement('label');
                                                radioLabel.htmlFor = `question_${question.id}_option_${optIndex}`;
                                                radioLabel.textContent = optionValue;
                                                
                                                optionDiv.appendChild(radioInput);
                                                optionDiv.appendChild(radioLabel);
                                                optionsContainer.appendChild(optionDiv);
                                            });
                                        }
                                        formGroup.appendChild(optionsContainer);
                                    } else if (question.type === 'multiple') {
                                        // Opciones múltiples (checkboxes)
                                        const optionsContainer = document.createElement('div');
                                        optionsContainer.className = 'checkbox-options-container mt-2';
                                        
                                        if (question.options) {
                                            // Convertir options a array si es necesario
                                            let optionsArray = [];
                                            if (Array.isArray(question.options)) {
                                                optionsArray = question.options;
                                            } else if (typeof question.options === 'string') {
                                                optionsArray = question.options.split(',');
                                            }
                                            
                                            // Para checkboxes, obtener respuestas del usuario como array
                                            let userResponses = [];
                                            if (question.user_response) {
                                                if (Array.isArray(question.user_response)) {
                                                    userResponses = question.user_response;
                                                } else if (typeof question.user_response === 'string') {
                                                    userResponses = question.user_response.split(',');
                                                }
                                            }
                                            
                                            optionsArray.forEach((option, optIndex) => {
                                                const optionValue = option.trim ? option.trim() : option;
                                                const isChecked = userResponses.includes(optionValue);
                                                
                                                const optionDiv = document.createElement('div');
                                                optionDiv.className = 'checkbox-option';
                                                
                                                const checkboxInput = document.createElement('input');
                                                checkboxInput.type = 'checkbox';
                                                checkboxInput.name = `answer_${question.id}[]`;
                                                checkboxInput.value = optionValue;
                                                checkboxInput.id = `question_${question.id}_option_${optIndex}`;
                                                if (isChecked) checkboxInput.checked = true;
                                                
                                                const checkboxLabel = document.createElement('label');
                                                checkboxLabel.htmlFor = `question_${question.id}_option_${optIndex}`;
                                                checkboxLabel.textContent = optionValue;
                                                
                                                optionDiv.appendChild(checkboxInput);
                                                optionDiv.appendChild(checkboxLabel);
                                                optionsContainer.appendChild(optionDiv);
                                            });
                                        }
                                        formGroup.appendChild(optionsContainer);
                                    }
                                    
                                    responseForm.appendChild(formGroup);
                                });
                                
                                // Agregar botones de envío
                                const buttonGroup = document.createElement('div');
                                buttonGroup.className = 'd-grid gap-2 mt-4';
                                buttonGroup.innerHTML = `
                                    <button type="submit" class="btn btn-success" id="submitResponseBtn">
                                        <i class="fas fa-paper-plane me-1"></i> {{ __("site.Submit Answers") }}
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" id="cancelResponseBtn">
                                        <i class="fas fa-times me-1"></i> {{ __("site.Cancel") }}
                                    </button>
                                `;
                                responseForm.appendChild(buttonGroup);
                            } else if (hasQuestions) {
                                // El evento tiene preguntas pero no está disponible para responder
                                responseSection.style.display = 'block';
                                responseStatus.textContent = '{{ __("site.Not available right now") }}';
                                responseStatus.className = 'badge bg-secondary';
                                responseForm.innerHTML = '';
                            } else {
                                // No hay preguntas → ocultar por completo la sección
                                responseSection.style.display = 'none';
                            }
                            // Mostrar el modal
                            eventModal.show();
                        })
                        .catch(error => {
                            console.error('Error al cargar los detalles del evento:', error);
                            alert('{{ __("site.Error loading event details") }}: ' + error.message);
                        });
                },

                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                locale: '{{ app()->getLocale() }}',
                firstDay: 1,
                buttonText: {
                    today: '{{ __("site.Today") }}',
                    month: '{{ __("site.Month") }}',
                    week: '{{ __("site.Week") }}',
                    day: '{{ __("site.Day") }}'
                }
            });
            
            calendar.render();
            
            // Aplicar filtro cuando cambie el tipo de evento
            eventTypeFilter.addEventListener('change', function() {
                calendar.refetchEvents();
            });
            
            // Manejar el envío del formulario de respuestas
            document.getElementById('responseForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Recopilar y validar todas las respuestas
                const responses = {};
                let hasErrors = false;
                
                // Obtener todas las preguntas del evento
                const questions = currentEventDetails.extendedProps.questions || [];
                
                // Validar cada pregunta
                questions.forEach(question => {
                    let answerValue;
                    
                    if (question.type === 'text') {
                        // Para campos de texto
                        const textarea = document.querySelector(`textarea[name="answer_${question.id}"]`);
                        answerValue = textarea ? textarea.value.trim() : '';
                        
                        // Validar campo requerido
                        if (question.required && !answerValue) {
                            markFieldAsError(textarea, 'Esta pregunta es obligatoria');
                            hasErrors = true;
                        } else {
                            clearFieldError(textarea);
                        }
                    } 
                    else if (question.type === 'single') {
                        // Para opciones únicas (radio buttons)
                        const selectedRadio = document.querySelector(`input[name="answer_${question.id}"]:checked`);
                        answerValue = selectedRadio ? selectedRadio.value : '';
                        
                        // Validar campo requerido
                        if (question.required && !answerValue) {
                            const radioContainer = document.querySelector(`.radio-options-container[name="answer_${question.id}"]`);
                            markFieldAsError(radioContainer, 'Esta pregunta es obligatoria');
                            hasErrors = true;
                        } else {
                            const radioContainer = document.querySelector(`.radio-options-container[name="answer_${question.id}"]`);
                            clearFieldError(radioContainer);
                        }
                    }
                    else if (question.type === 'multiple') {
                        // Para opciones múltiples (checkboxes)
                        const checkboxes = document.querySelectorAll(`input[name="answer_${question.id}[]"]:checked`);
                        answerValue = Array.from(checkboxes).map(cb => cb.value);
                        
                        // Validar campo requerido
                        if (question.required && answerValue.length === 0) {
                            const checkboxContainer = document.querySelector(`.checkbox-options-container[name="answer_${question.id}"]`);
                            markFieldAsError(checkboxContainer, 'Esta pregunta es obligatoria');
                            hasErrors = true;
                        } else {
                            const checkboxContainer = document.querySelector(`.checkbox-options-container[name="answer_${question.id}"]`);
                            clearFieldError(checkboxContainer);
                        }
                    }
                    
                    // Solo agregar respuestas no vacías
                    if (answerValue && (!Array.isArray(answerValue) || answerValue.length > 0)) {
                        responses[question.id] = answerValue;
                    }
                });
                
                // Si hay errores, no enviar el formulario
                if (hasErrors) {
                    alert('Por favor, complete todas las preguntas obligatorias.');
                    return;
                }
                
                // Verificar que hay al menos una respuesta
                if (Object.keys(responses).length === 0) {
                    alert('No hay respuestas para guardar.');
                    return;
                }
                
                // Mostrar indicador de carga
                const submitBtn = document.getElementById('submitResponseBtn');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
                submitBtn.disabled = true;
                
                // Enviar respuestas al servidor
                fetch('{{ route("calendar.event.answers") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        event_id: currentEventDetails.id,
                        responses: responses
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        // Si la respuesta no es exitosa, obtener más detalles del error
                        return response.text().then(text => {
                            throw new Error(`Error del servidor: ${response.status} - ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('{{ __("site.Answers submitted successfully") }}');
                        eventModal.hide();
                    } else {
                        throw new Error(data.message || 'Error desconocido del servidor');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al enviar las respuestas: ' + error.message);
                })
                .finally(() => {
                    // Restaurar el botón a su estado original
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });

            // Función para marcar un campo como error
            function markFieldAsError(element, message) {
                if (!element) return;
                
                element.classList.add('is-invalid');
                
                // Eliminar mensaje de error anterior si existe
                const existingError = element.nextElementSibling;
                if (existingError && existingError.classList.contains('error-message')) {
                    existingError.remove();
                }
                
                // Agregar mensaje de error
                const errorElement = document.createElement('div');
                errorElement.className = 'error-message text-danger small mt-1';
                errorElement.textContent = message;
                element.parentNode.appendChild(errorElement);
            }

            // Función para limpiar el error de un campo
            function clearFieldError(element) {
                if (!element) return;
                
                element.classList.remove('is-invalid');
                
                // Eliminar mensaje de error si existe
                const errorElement = element.nextElementSibling;
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.remove();
                }
            }

            // Manejar la cancelación de respuestas
            document.getElementById('cancelResponseBtn').addEventListener('click', function() {
                if (confirm('{{ __("site.Are you sure you want to cancel your answers? All entered data will be lost.") }}')) {
                    document.getElementById('responseForm').reset();
                }
            });
        });
    </script>
</body>
</html>