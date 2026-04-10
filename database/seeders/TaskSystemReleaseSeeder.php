<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReleaseNote;

class TaskSystemReleaseSeeder extends Seeder
{
    public function run()
    {
        ReleaseNote::create([
            'title' => 'Sistema de Gestió de Tasques - Kanban Board',
            'slug' => 'tasques-kanban-board-v1.0.0',
            'version' => '1.0.0',
            'type' => 'major',
            'status' => 'published',
            'summary' => 'Implementació completa del sistema de gestió de tasques amb Kanban board, drag & drop i assignació avançada per rols.',
            'content' => '# 🎯 Sistema de Gestió de Tasques - Versió 1.0.0

Estem molt contents de presentar el nou sistema de gestió de tasques completament funcional per al Campus Virtual!

## ✨ Novetats Principals

### 📋 Kanban Board Interactiu
- **Drag & Drop** - Mou les tasques entre columnes fàcilment
- **Visualització en temps real** - Les tasques s\'actualitzen automàticament
- **Colors per prioritat** - Identifica ràpidament les tasques urgents
- **Avatars d\'usuaris** - Veu qui té assignada cada tasca

### 👥 Assignació Avançada d\'Usuaris
- **Cerca intel·ligent** - Troba usuaris per nom o correu
- **Selecció per rols** - Assigna a grups específics (Professorat, Alumnes, etc.)
- **Comptadors d\'usuaris** - Sap quantes persones hi ha a cada rol
- **Selecció ràpida** - Sistema intuïtiu sense llistes massives

### 📊 Dashboard Integrat
- **Card de tasques** - Estadístiques en temps real al dashboard admin
- **Comptadors correctes** - Exclou tasques esborrades (soft deletes)
- **Visualització per taulers** - Tots els teus projectes en un sol lloc

### 🔧 Gestió de Taulers
- **Creació de taulers** - Configura els teus propis espais de treball
- **Diferents tipus** - Cursos, Equips, Globals, Departaments
- **Assignació de responsables** - Defineix qui gestiona cada tauler

## 🚀 Funcionalitats Tècniques

### API Endpoints
- `POST /api/tasks` - Crear noves tasques
- `PUT /api/tasks/{id}/move` - Moure tasques entre columnes
- `GET /api/users/by-role` - Usuaris agrupats per rol
- `GET /api/users/role/{role}` - Usuaris d\'un rol específic

### Seguretat i Validació
- **Protecció CSRF** - Totes les peticions segures
- **Autorització per rols** - Accés controlat segons permisos
- **Validació de dades** - Entrades verificades al servidor

### UI/UX Millorada
- **Responsive** - Funciona en mòbils, tablets i desktop
- **Transicions suaus** - Animacions professionals
- **Colors consistents** - Disseny coherent amb el Campus

## 📈 Estadístiques del Sistema

- **3 Taulers** creats i funcionals
- **12 Llistes** organitzades
- **14 Tasques** individuals gestionades
- **12+ Rols** del sistema integrats
- **600+ Usuaris** a la base de dades

## 🎯 Beneficis per als Usuaris

### Per a Professors
- Organitza les teves tasques per assignatura
- Assigna tasques als alumnes fàcilment
- Segueix el progrés en temps real

### Per a Alumnes
- Veu totes les teves tasques en un sol lloc
- Identifica ràpidament les prioritats
- Col·labora en projectes d\'equip

### Per a Administració
- Supervisa el progrés global
- Assigna responsable per projecte
- Genera informes automàticament

## 🔗 Enllaços Ràpids

- **Dashboard Principal**: https://dev.upg.cat/dashboard
- **Llistat de Taulers**: https://dev.upg.cat/tasques
- **Crear Nou Tauler**: https://dev.upg.cat/tasques/crear
- **Exemple Kanban**: https://dev.upg.cat/tasques/tauler/1

## 🐛 Correccions Implementades

- ✅ Error 419 CSRF en creació de tasques
- ✅ Error 405 Method Not Allowed en API
- ✅ Error 500 Internal Server en validacions
- ✅ Variables undefined a vistes Blade
- ✅ Errors Alpine.js amb valors null
- ✅ Comptadors incorrectes a dashboard

## 🔄 Pròximes Millores

- Sistema de notificacions per tasques
- Integració amb calendari acadèmic
- Exportació de dades a PDF/Excel
- Sistema de comentaris en tasques
- Fitxers adjunts per tasca

---

**🎉 El sistema de tasques està 100% funcional i llest per utilitzar-se!**

Per a qualsevol dubte o problema, contacta amb el suport tècnic.',
            'features' => [
                'Kanban board amb drag & drop',
                'Assignació de tasques per rol',
                'Dashboard integrat amb estadístiques',
                'Gestió de taulers múltiples',
                'Cerca intel·ligent d\'usuaris',
                'Sistema de prioritats',
                'Visualització d\'avatars',
                'Comptadors en temps real'
            ],
            'improvements' => [
                'Optimització de consultes a base de dades',
                'Millora de la UX en selecció d\'usuaris',
                'Implementació de caché per millor rendiment',
                'Correcció d\'errors CSRF',
                'Validació millorada de dades',
                'Disseny responsive complet'
            ],
            'fixes' => [
                'Error 419 CSRF token missing',
                'Error 405 Method Not Allowed',
                'Error 500 Internal Server Error',
                'Variables undefined a Blade',
                'Errors Alpine.js null references',
                'Comptadors incorrectes a dashboard'
            ],
            'breaking_changes' => [],
            'affected_modules' => ['tasques', 'dashboard', 'usuaris', 'rutes'],
            'target_audience' => ['professors', 'alumnes', 'administradors', 'gestors'],
            'commits' => [
                'feat: Sistema complet de gestió de tasques per Campus',
                'fix: Error 419 CSRF en creació i moviment de tasques',
                'fix: Error 405 Method Not Allowed en API endpoints',
                'fix: Error 500 Internal Server en validacions',
                'fix: Variables undefined a vistes Blade',
                'fix: Errors Alpine.js amb valors null',
                'fix: Comptadors incorrectes a dashboard'
            ],
            'published_at' => now(),
            'created_by' => 1, // Admin user
            'published_by' => 1, // Admin user
        ]);
    }
}
