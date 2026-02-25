<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CampusStudent;
use App\Models\CampusTeacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Usuari ADMINISTRADOR
        $admin = User::firstorcreate([
            'name' => 'Administrador Centre',
            'email' => 'admin@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'email_verified_at' => Carbon::now(),
            'locale' => 'ca',
        ]);
        $admin->assignRole('admin');

        // Usuari GESTOR
        $gestor = User::firstorcreate([
            'name' => 'Gemma Gestió',
            'email' => 'gestio@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'email_verified_at' => Carbon::now(),
            'locale' => 'ca',
        ]);
        $gestor->assignRole('gestor');

        // Usuari TRESORERIA
        $tresoreria = User::firstorcreate([
            'name' => 'AAA',
            'email' => 'tresoreria@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'email_verified_at' => Carbon::now(),
            'locale' => 'ca',
        ]);
        $tresoreria->assignRole('treasury');
 

        // Usuari EDITOR 1
        $editor1 = User::firstorcreate([
            'name' => 'Eduard Editor',
            'email' => 'editor@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'email_verified_at' => Carbon::now(),
            'locale' => 'ca',
        ]);
        $editor1->assignRole('editor');

        // Usuari EDITOR 2
        $editor2 = User::firstorcreate([
            'name' => 'Elisabet Edició',
            'email' => 'editora@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'locale' => 'ca',
        ]);
        $editor2->assignRole('editor');

        // PROFESSOR 1
        $teacher1 = User::firstorcreate([
            'name' => 'Joan Prat i Soler',
            'email' => 'teacher@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'email_verified_at' => Carbon::now(),
            'locale' => 'ca',
            
        ]);
        $teacher1->assignRole('teacher');
        
        // Crear perfil de professor
        $teacherProfile1 = CampusTeacher::firstorcreate([
            'user_id' => $teacher1->id,
            'teacher_code' => 'PROF005',
            'first_name' => 'Joan',
            'last_name' => 'Prat i Soler',
            'dni' => '12345678A',
            'email' => 'teacher@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'phone' => '+34 600 111 222',
            'address' => 'Carrer Major 1, Sant Cugat',
            'postal_code' => '08001',
            'city'=> 'Barcelona',
            'iban' => 'ES1234567890123456789012',
            'bank_titular' => 'Joan Prat i Soler',
            'specialization' => 'Informàtica',
            'title' => 'Dr.',
            'areas' => ['Programació', 'Bases de Dades'],
            'status' => 'active',
            'hiring_date' => '2023-09-01',
        ]);

        // PROFESSORA 2
        $teacher2 = User::firstorcreate([
            'name' => 'Maria García i López',
            'email' => 'teacher2@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'locale' => 'ca',
        ]);
        $teacher2->assignRole('teacher');
        
        $teacherProfile2 = CampusTeacher::firstorcreate([
            'user_id' => $teacher2->id,
            'teacher_code' => 'PROF006',
            'first_name' => 'Maria',
            'last_name' => 'García i López',
            'dni' => '87654321B',
            'email' => 'profe@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'phone' => '+34 600 333 444',
            'address' => 'Carrer del Poble 2, 2-3',
            'postal_code' => '08401',
            'city'=> 'Granollers, Barcelona',
            'iban' => 'ES1234567890123456789999',
            'bank_titular' => 'Maria Garcia i Lopez',
            'specialization' => 'Matemàtiques',
            'title' => 'Dra.',
            'areas' => ['Àlgebra', 'Càlcul'],
            'status' => 'active',
            'hiring_date' => '2023-09-01',
        ]);

        // ESTUDIANT 1
        $student1 = User::firstorcreate([
            'name' => 'Anna Martínez i Roca',
            'email' => 'alumne@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'email_verified_at' => Carbon::now(),
            'locale' => 'ca',
        ]);
        $student1->assignRole('student');
        
        $studentProfile1 = CampusStudent::firstorcreate([
            'user_id' => $student1->id,
            'student_code' => 'STD007',
            'first_name' => 'Anna',
            'last_name' => 'Martínez i Roca',
            'dni' => '11223344C',
            'birth_date' => '2000-05-15',
            'phone' => '+34 600 555 666',
            'address' => 'Carrer Principal 123, Barcelona',
            'email' => 'alumne@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'emergency_contact' => 'Pare - Josep Martínez',
            'emergency_phone' => '+34 600 777 888',
            'status' => 'active',
            'enrollment_date' => '2024-09-01',
        ]);

        // ESTUDIANT 2
        $student2 = User::firstorcreate([
            'name' => 'Carles Ruiz i Navarro',
            'email' => 'student@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'locale' => 'ca',
        ]);
        $student2->assignRole('student');
        
        $studentProfile2 = CampusStudent::firstorcreate([
            'user_id' => $student2->id,
            'student_code' => 'EST008',
            'first_name' => 'Carles',
            'last_name' => 'Ruiz i Navarro',
            'dni' => '55667788D',
            'birth_date' => '2001-03-22',
            'phone' => '+34 600 999 000',
            'address' => 'Avinguda Central 456, L\'Hospitalet',
            'email' => 'student@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'emergency_contact' => 'Mare - Laura Navarro',
            'emergency_phone' => '+34 600 111 222',
            'status' => 'active',
            'enrollment_date' => '2024-09-01',
        ]);

        // USUARI BÀSIC 1
        $user1 = User::firstorcreate([
            'name' => 'Usuari Bàsic',
            'email' => 'usuari@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'email_verified_at' => Carbon::now(),
            'locale' => 'ca',
        ]);
        $user1->assignRole('user');

        // USUARI BÀSIC 2
        $user2 = User::firstorcreate([
            'name' => 'Usuari Secundari',
            'email' => 'usuari2@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'locale' => 'ca',
        ]);
        $user2->assignRole('user');

        // CONVIDAT 1
        $invited1 = User::firstorcreate([
            'name' => 'Convidat Extern',
            'email' => 'convidat@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'email_verified_at' => Carbon::now(),
            'locale' => 'ca',
        ]);
        $invited1->assignRole('invited');

        // CONVIDAT 2
        $invited2 = User::firstorcreate([
            'name' => 'Col·laborador Temporal',
            'email' => 'collaborador@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
            'locale' => 'ca',
        ]);
        $invited2->assignRole('invited');
    }
}