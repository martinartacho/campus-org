<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\EventQuestion;
use Carbon\Carbon;

class EventExampleSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        $thirtyDaysLater = $now->copy()->addDays(30);
        
        // Crear tipo de evento si no existe
       $eventTypeId = DB::table('event_types')->insertGetId([
            'name' => 'Reunión',
            'color' => '#3c8dbc',
            'is_default' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $eventTypeId = DB::table('event_types')->insertGetId([
            'name' => 'Cita',
            'color' => '#4bbc3cff',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $eventTypeId = DB::table('event_types')->insertGetId([
            'name' => 'Espacio',
            'color' => '#c6ff7cff',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Crear evento
        $eventId = DB::table('events')->insertGetId([
            'title' => "Prueba single",
            'description' => "Reunion semanal se requiera confirmar asistencia y elegir menú",
            'start' => $thirtyDaysLater->copy()->setTime(12, 0, 0),
            'end' => $thirtyDaysLater->copy()->setTime(15, 0, 0),
            'color' => "#3c8dbc",
            'max_users' => 10,
            'visible' => 1,
            'start_visible' => $now->copy()->setTime(9, 0, 0),
            'end_visible' => $thirtyDaysLater->copy()->setTime(15, 0, 0),
            'event_type_id' => $eventTypeId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Crear preguntas para el evento
        $questions = [
            [
                'event_id' => $eventId,
                'question' => "Asistencia",
                'type' => "single",
                'options' => json_encode(["Si", "No", "Potser"]),
                'required' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'event_id' => $eventId,
                'question' => "Elige menú",
                'type' => "multiple",
                'options' => json_encode(["Ensalada", "Verdura", "Carne", "Pescado", "Flan", "Fruta"]),
                'required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'event_id' => $eventId,
                'question' => "Indica alergias o intolerancias",
                'type' => "text",
                'options' => null,
                'required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        DB::table('event_questions')->insert($questions);

        // Crear algunas plantillas de ejemplo
        $templates = [
            [
                'question' => "Confirmación de asistencia",
                'type' => "single",
                'options' => json_encode(["Sí asistiré", "No asistiré", "Quizás"]),
                'required' => 1,
                'is_template' => 1,
                'template_name' => "Confirmación de Asistencia",
                'template_description' => "Plantilla para confirmar asistencia a eventos",
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => "Preferencias alimentarias",
                'type' => "multiple",
                'options' => json_encode(["Vegetariano", "Vegano", "Sin gluten", "Sin lactosa", "Sin restricciones"]),
                'required' => 0,
                'is_template' => 1,
                'template_name' => "Preferencias Alimentarias",
                'template_description' => "Plantilla para recoger preferencias dietéticas",
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => "Haz tu reserva",
                'type' => "multiple",
                'options' => json_encode(["Individual", "Para 2 personas", "Estandar 4 personas", "Sin lactosa", "Más de 4"]),
                'required' => 1,
                'is_template' => 1,
                'template_name' => "Reservar espacio",
                'template_description' => "Reservar espacio en coworking, restaurante, oficina, colegio, gimansio",
                'created_at' => $now,
                'updated_at' => $now,
            ]

        ];

        DB::table('event_question_templates')->insert($templates);
    }
}