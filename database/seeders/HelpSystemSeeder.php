<?php

namespace Database\Seeders;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Database\Seeder;

class HelpSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Crear categorías por área
        $categories = [
            // Cursos
            ['name' => 'Crear curso', 'area' => 'cursos', 'icon' => 'bi-plus-circle', 'order' => 1],
            ['name' => 'Editar curso', 'area' => 'cursos', 'icon' => 'bi-pencil', 'order' => 2],
            ['name' => 'Listar cursos', 'area' => 'cursos', 'icon' => 'bi-list', 'order' => 3],
            ['name' => 'Gestionar profesores', 'area' => 'cursos', 'icon' => 'bi-people', 'order' => 4],
            
            // Matrícula
            ['name' => 'Nueva matrícula', 'area' => 'matricula', 'icon' => 'bi-person-plus', 'order' => 1],
            ['name' => 'Aprobar matrícula', 'area' => 'matricula', 'icon' => 'bi-check-circle', 'order' => 2],
            ['name' => 'Listar matrículas', 'area' => 'matricula', 'icon' => 'bi-list-ul', 'order' => 3],
            ['name' => 'Cancelar matrícula', 'area' => 'matricula', 'icon' => 'bi-x-circle', 'order' => 4],
            
            // Materiales
            ['name' => 'Subir material', 'area' => 'materiales', 'icon' => 'bi-upload', 'order' => 1],
            ['name' => 'Editar material', 'area' => 'materiales', 'icon' => 'bi-pencil-square', 'order' => 2],
            ['name' => 'Listar materiales', 'area' => 'materiales', 'icon' => 'bi-folder', 'order' => 3],
            ['name' => 'Eliminar material', 'area' => 'materiales', 'icon' => 'bi-trash', 'order' => 4],
            
            // Configuración
            ['name' => 'Perfil', 'area' => 'configuracion', 'icon' => 'bi-person', 'order' => 1],
            ['name' => 'Preferencias', 'area' => 'configuracion', 'icon' => 'bi-gear', 'order' => 2],
            ['name' => 'Sistema', 'area' => 'configuracion', 'icon' => 'bi-cpu', 'order' => 3],
            ['name' => 'Notificaciones', 'area' => 'configuracion', 'icon' => 'bi-bell', 'order' => 4],
        ];

        foreach ($categories as $category) {
            HelpCategory::create($category);
        }

        // Crear artículos de ayuda básicos
        $articles = [
            // Cursos
            [
                'title' => 'Cómo crear un nuevo curso',
                'slug' => 'como-crear-nuevo-curso',
                'content' => 'Para crear un nuevo curso:\n1. Ve a la sección Cursos\n2. Haz clic en "Nuevo Curso"\n3. Completa los datos del curso\n4. Asigna profesores\n5. Guarda el curso',
                'area' => 'cursos',
                'context' => 'admin.courses.create',
                'type' => 'guia',
                'status' => 'validated',
                'order' => 1,
                'created_by' => 1, // Admin user
            ],
            [
                'title' => 'Cómo editar un curso existente',
                'slug' => 'como-editar-curso-existente',
                'content' => 'Para editar un curso:\n1. Ve a la lista de cursos\n2. Busca el curso que quieres editar\n3. Haz clic en el botón "Editar"\n4. Modifica los datos necesarios\n5. Guarda los cambios',
                'area' => 'cursos',
                'context' => 'admin.courses.edit',
                'type' => 'guia',
                'status' => 'validated',
                'order' => 2,
                'created_by' => 1,
            ],
            
            // Matrícula
            [
                'title' => 'Cómo aprobar una matrícula',
                'slug' => 'como-aprobar-matricula',
                'content' => 'Para aprobar una matrícula:\n1. Ve a la sección Matrículas\n2. Busca la matrícula pendiente\n3. Revisa los datos del estudiante\n4. Haz clic en "Aprobar"\n5. Confirma la aprobación',
                'area' => 'matricula',
                'context' => 'admin.registrations.index',
                'type' => 'procedimiento',
                'status' => 'validated',
                'order' => 1,
                'created_by' => 1,
            ],
            
            // Materiales
            [
                'title' => 'Cómo subir materiales a un curso',
                'slug' => 'como-subir-materiales-curso',
                'content' => 'Para subir materiales:\n1. Ve al curso deseado\n2. Haz clic en "Materiales"\n3. Presiona "Subir Material"\n4. Selecciona el archivo\n5. Añade descripción\n6. Sube el archivo',
                'area' => 'materiales',
                'context' => 'admin.courses.materials',
                'type' => 'procedimiento',
                'status' => 'validated',
                'order' => 1,
                'created_by' => 1,
            ],
            
            // Configuración
            [
                'title' => 'Cómo configurar tu perfil',
                'slug' => 'como-configurar-perfil',
                'content' => 'Para configurar tu perfil:\n1. Ve a "Mi Perfil"\n2. Edita tu información personal\n3. Sube tu foto\n4. Actualiza tus datos\n5. Guarda los cambios',
                'area' => 'configuracion',
                'context' => 'profile.edit',
                'type' => 'guia',
                'status' => 'validated',
                'order' => 1,
                'created_by' => 1,
            ],
            
            // Sistema de Ayuda
            [
                'title' => 'Cómo crear un artículo de ayuda',
                'slug' => 'como-crear-articulo-ayuda',
                'content' => '<h2>Para crear un nuevo artículo de ayuda:</h2>\n\n<ol>\n<li><strong>Accede al sistema de ayuda</strong><br>\n   Ve a <code>/campus/help</code> y haz clic en "Nou Article"</li>\n\n<li><strong>Completa la información básica</strong><br>\n   • <strong>Títol:</strong> Títol clar i descriptiu<br>\n   • <strong>Àrea:</strong> Selecciona l\'àrea (Cursos, Matrícula, Materiales, Configuración)<br>\n   • <strong>Context:</strong> On s\'aplica aquest article (opcional)<br>\n   • <strong>Tipus:</strong> Tipus d\'article (opcional)<br>\n   • <strong>Categoria:</strong> Selecciona una categoria existent (opcional)<br>\n   • <strong>Estat:</strong> Borrador, Validat o Obsolet<br>\n   • <strong>Ordre:</strong> Ordre de visualització (opcional)</li>\n\n<li><strong>Escriu el contingut</strong><br>\n   Utilitza l\'editor WYSIWYG per crear el teu article:<br>\n   • <strong>Format de text:</strong> Negreta, cursiva, subratllat<br>\n   • <strong>Llistes:</strong> Numerades i amb vinyetes<br>\n   • <strong>Enllaços:</strong> Per vincular a altres pàgines<br>\n   • <strong>Imatges:</strong> Per afegir captures de pantalla<br>\n   • <strong>Taules:</strong> Per organitzar informació<br>\n   • <strong>Codi:</strong> Per mostrar fragments de codi</li>\n\n<li><strong>Revisa i publica</strong><br>\n   • Revisa el contingut per detectar errors<br>\n   • Canvia l\'estat a "Validat" per publicar<br>\n   • Guarda l\'article</li>\n\n<li><strong>Consells addicionals</strong><br>\n   • Utilitza títols clars i descriptius<br>\n   • Inclou captures de pantalla quan sigui possible<br>\n   • Estructura el contingut amb encapçalaments<br>\n   • Revisa l\'ortografia i la gramàtica<br>\n   • Afegeix exemples pràctics</li>\n</ol>\n\n<h3>📌 Millors pràctiques</h3>\n<ul>\n<li>Utilitza un llenguatge clar i senzill</li>\n<li>Divideix el contingut en seccions fàcils de llegir</li>\n<li>Inclou exemples concrets del sistema</li>\n<li>Mantingues l\'article actualitzat</li>\n<li>Utilitza categories apropiades per organitzar</li>\n</ul>',
                'area' => 'configuracion',
                'context' => 'admin.help.articles.create',
                'type' => 'guia',
                'status' => 'validated',
                'order' => 1,
                'created_by' => 1,
            ],
        ];

        foreach ($articles as $article) {
            HelpArticle::create($article);
        }

        $this->command->info('✅ Sistema de ayuda clásico creado exitosamente');
    }
}
