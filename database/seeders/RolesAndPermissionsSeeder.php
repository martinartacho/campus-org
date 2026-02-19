<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Netejar memòria cau
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // PERMISOS DEL SISTEMA BÀSIC (EXISTENTS)
        $basicPermissions = [
            // Sistema
            'admin.access',
            'settings.edit',
            
            // Usuaris
            'users.index',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Rols i permisos
            'roles.index',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.index',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            
            // Notificacions
            'notifications.publish',
            'notifications.index',
            'notifications.create',
            'notifications.edit',
            'notifications.delete',
            'notifications.view',
            
            // Esdeveniments
            'events.index',
            'events.view',
            'events.create',
            'events.edit',
            'events.delete',
            'event_types.index',
            'event_types.view',
            'event_types.create',
            'event_types.edit',
            'event_types.delete',
            'event_questions.index',
            'event_questions.view',
            'event_questions.create',
            'event_questions.edit',
            'event_questions.delete',
            'event_answers.index',
            'event_answers.view',
            'event_answers.create',
            'event_answers.edit',
            'event_answers.delete',
            'event_question_templates.index',
            'event_question_templates.view',
            'event_question_templates.create',
            'event_question_templates.edit',
            'event_question_templates.delete',
        ];

        // PERMISOS DEL CAMPUS EDUCATIU (NOUS)
        $campusPermissions = [
            // Categories
            'campus.categories.index',
            'campus.categories.view',
            'campus.categories.create',
            'campus.categories.edit',
            'campus.categories.delete',
            
            // Temporades
            'campus.seasons.index',
            'campus.seasons.view',
            'campus.seasons.create',
            'campus.seasons.edit',
            'campus.seasons.delete',
            
            // Cursos
            'campus.courses.index',
            'campus.courses.view',
            'campus.courses.create',
            'campus.courses.edit',
            'campus.courses.delete',
            'campus.courses.enroll',
            'campus.courses.manage',
            
            // Estudiants
            'campus.students.index',
            'campus.students.view',
            'campus.students.create',
            'campus.students.edit',
            'campus.students.delete',
            'campus.students.manage',
            
            // Professors
            'campus.teachers.index',
            'campus.teachers.view',
            'campus.teachers.create',
            'campus.teachers.edit',
            'campus.teachers.delete',
            'campus.teachers.assign',
            
            // Matriculacions/Registres
            'campus.registrations.index',
            'campus.registrations.view',
            'campus.registrations.create',
            'campus.registrations.edit',
            'campus.registrations.delete',
            'campus.registrations.approve',
            'campus.registrations.manage',
            
            // Pagaments
            'campus.payments.view',
            'campus.payments.manage',
            'campus.payments.approve',
            'campus.payments.export',
            
            // Vista de perfil propi
            'campus.profile.view',
            'campus.profile.edit',
            
            // Vista de cursos propis (per a professors i estudiants)
            'campus.my_courses.view',
            'campus.my_courses.manage',
            
            // Permisos de tesorería adicionales
            'campus.consents.request',
            'campus.consents.view',
            'campus.reports.financial',
            
            // Permisos financieros de profesores
            'campus.teachers.financial_data.view',
            'campus.teachers.financial_data.update',
        ];

        // COMBINAR TOTS ELS PERMISOS
        $allPermissions = array_merge($basicPermissions, $campusPermissions);
        
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ROL: ADMINISTRADOR (TOT)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // ROL: GESTOR (amb permisos del campus)
        $gestor = Role::firstOrCreate(['name' => 'gestor']);
        $gestorPermissions = [
            // Permisos bàsics existents
            'users.index', 'users.create', 'users.edit', 'users.delete',
            'notifications.index', 'notifications.create', 'notifications.edit', 
            'notifications.delete', 'notifications.view',
            'events.index', 'events.view', 'events.create', 'events.edit', 'events.delete',
            'event_questions.index', 'event_questions.view', 'event_questions.create', 
            'event_questions.edit', 'event_questions.delete',
            'event_question_templates.index', 'event_question_templates.view', 
            'event_question_templates.create', 'event_question_templates.edit', 
            'event_question_templates.delete',
            
            // Nous permisos del campus
            'campus.categories.index', 'campus.categories.view',
            'campus.seasons.index', 'campus.seasons.view',
            'campus.courses.index', 'campus.courses.view', 'campus.courses.create',
            'campus.courses.edit', 'campus.courses.delete', 'campus.courses.manage',
            'campus.students.index', 'campus.students.view', 'campus.students.create',
            'campus.students.edit', 'campus.students.delete', 'campus.students.manage',
            'campus.teachers.index', 'campus.teachers.view', 'campus.teachers.create',
            'campus.teachers.edit', 'campus.teachers.delete', 'campus.teachers.assign',
            'campus.registrations.index', 'campus.registrations.view', 'campus.registrations.create',
            'campus.registrations.edit', 'campus.registrations.delete', 'campus.registrations.manage',
            'campus.payments.view', 'campus.payments.manage',
        ];
        $gestor->syncPermissions($gestorPermissions);

        // ROL: TREASURY (Tresoreria / Administració Econòmica)
        $treasury = Role::firstOrCreate(['name' => 'treasury']);
        $treasuryPermissions = [
            // Pagaments
            'campus.payments.view',
            'campus.payments.manage',
            'campus.payments.approve',
            'campus.payments.export',
            
            // Professorat (dades econòmiques)
            'campus.teachers.view',
            'campus.teachers.financial_data.view',
            'campus.teachers.financial_data.update',
            
            // CRUD Teachers
            'campus.teachers.index',
            'campus.teachers.create',
            'campus.teachers.edit',
            'campus.teachers.delete',
            'campus.teachers.assign',
            
            // CRUD Courses (para poder crear nuevos cursos desde teacher creation)
            'campus.courses.view',
            'campus.courses.create',
            'campus.courses.edit',
            'campus.courses.delete',
            
            // Consentiments RGPD
            'campus.consents.request',
            'campus.consents.view',
            
            // Informes
            'campus.reports.financial',
        ];
        $treasury->syncPermissions($treasuryPermissions);

    

        // ROL: EDITOR (només contingut)
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editorPermissions = [
            'notifications.index', 'notifications.create', 'notifications.edit',
            'notifications.delete', 'notifications.view',
            'events.index', 'events.view', 'events.create', 'events.edit', 'events.delete',
        ];
        $editor->syncPermissions($editorPermissions);

        // ROL: PROFESSOR (NOU - Professor)
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $teacherPermissions = [
            'campus.profile.view', 'campus.profile.edit',
            'campus.my_courses.view', 'campus.my_courses.manage',
            'campus.students.view',  // Veure estudiants dels seus cursos
            'campus.registrations.view',  // Veure matriculacions dels seus cursos
            'notifications.view', 'notifications.create',
        ];
        $teacher->syncPermissions($teacherPermissions);

        // ROL: ESTUDIANT (NOU - Estudiant)
        $student = Role::firstOrCreate(['name' => 'student']);
        $studentPermissions = [
            'campus.profile.view', 'campus.profile.edit',
            'campus.my_courses.view',
            'notifications.view', 'notifications.create',
        ];
        $student->syncPermissions($studentPermissions);

        // ROL: USER (usuari bàsic registrat)
        $user = Role::firstOrCreate(['name' => 'user']);
        $userPermissions = [
            'notifications.view', 'notifications.create',
            'campus.profile.view', 'campus.profile.edit',
        ];
        $user->syncPermissions($userPermissions);

        // ROL: CONVIDAT (convidat - sense permisos específics)
        Role::firstOrCreate(['name' => 'invited']);

        // Assignar administrador a l'usuari amb ID = 1
        $user1 = \App\Models\User::find(1);
        if ($user1 && !$user1->hasRole('admin')) {
            $user1->assignRole('admin');
        }
    }
}