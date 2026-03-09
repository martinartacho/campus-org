<?php

namespace Database\Seeders;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Database\Seeder;

class HelpSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Crear categories per àrea
        $categories = [
            // Cursos
            ['name' => 'Crear curs', 'area' => 'cursos', 'icon' => 'bi-plus-circle', 'order' => 1],
            ['name' => 'Editar curs', 'area' => 'cursos', 'icon' => 'bi-pencil', 'order' => 2],
            ['name' => 'Llistar cursos', 'area' => 'cursos', 'icon' => 'bi-list', 'order' => 3],
            ['name' => 'Gestionar professors', 'area' => 'cursos', 'icon' => 'bi-people', 'order' => 4],
            
            // Matrícula
            ['name' => 'Nova matrícula', 'area' => 'matricula', 'icon' => 'bi-person-plus', 'order' => 1],
            ['name' => 'Aprovar matrícula', 'area' => 'matricula', 'icon' => 'bi-check-circle', 'order' => 2],
            ['name' => 'Llistar matrícules', 'area' => 'matricula', 'icon' => 'bi-list-ul', 'order' => 3],
            ['name' => 'Cancel·lar matrícula', 'area' => 'matricula', 'icon' => 'bi-x-circle', 'order' => 4],
            
            // Materials
            ['name' => 'Pujar material', 'area' => 'materials', 'icon' => 'bi-upload', 'order' => 1],
            ['name' => 'Editar material', 'area' => 'materials', 'icon' => 'bi-pencil-square', 'order' => 2],
            ['name' => 'Llistar materials', 'area' => 'materials', 'icon' => 'bi-folder', 'order' => 3],
            ['name' => 'Eliminar material', 'area' => 'materials', 'icon' => 'bi-trash', 'order' => 4],
            
            // Configuració
            ['name' => 'Perfil', 'area' => 'configuracio', 'icon' => 'bi-person', 'order' => 1],
            ['name' => 'Preferències', 'area' => 'configuracio', 'icon' => 'bi-gear', 'order' => 2],
            ['name' => 'Sistema', 'area' => 'configuracio', 'icon' => 'bi-cpu', 'order' => 3],
            ['name' => 'Notificacions', 'area' => 'configuracio', 'icon' => 'bi-bell', 'order' => 4],
        ];

        foreach ($categories as $category) {
            HelpCategory::firstOrCreate([
                'name' => $category['name'],
                'area' => $category['area']
            ], $category);
        }

        // Crear articles d'ajuda bàsics
        $articles = [
            // Cursos
            [
                'title' => 'Com crear un nou curs',
                'slug' => 'com-crear-nou-curs',
                'content' => '<h3>Per crear un nou curs:</h3><ol><li>Vés a la secció <strong>Cursos</strong></li><li>Fes clic a <strong>"Nou Curs"</strong></li><li>Completa les dades del curs</li><li>Assigna professors</li><li>Guarda el curs</li></ol>',
                'area' => 'cursos',
                'context' => 'admin.courses.create',
                'type' => 'guia',
                'status' => 'validated',
                'order' => 1,
                'created_by' => 1, // Admin user
            ],
            [
                'title' => 'Com editar un curs existent',
                'slug' => 'com-editar-curs-existent',
                'content' => '<h3>Per editar un curs:</h3><ol><li>Vés a la llista de cursos</li><li>Busca el curs que vols editar</li><li>Fes clic al botó <strong>"Editar"</strong></li><li>Modifica les dades necessàries</li><li>Guarda els canvis</li></ol>',
                'area' => 'cursos',
                'context' => 'admin.courses.edit',
                'type' => 'guia',
                'status' => 'validated',
                'order' => 2,
                'created_by' => 1,
            ],
            
            // Matrícula
            [
                'title' => 'Com aprovar una matrícula',
                'slug' => 'com-aprovar-matricula',
                'content' => '<h3>Per aprovar una matrícula:</h3><ol><li>Vés a la secció <strong>Matrícules</strong></li><li>Busca la matrícula pendent</li><li>Revisa les dades de l\'estudiant</li><li>Fes clic a <strong>"Aprovar"</strong></li><li>Confirma l\'aprovació</li></ol>',
                'area' => 'matricula',
                'context' => 'admin.registrations.index',
                'type' => 'procediment',
                'status' => 'validated',
                'order' => 1,
                'created_by' => 1,
            ],
            
            // Materials
            [
                'title' => 'Com pujar materials a un curs',
                'slug' => 'com-pujar-materials-curs',
                'content' => '<h3>Per pujar materials:</h3><ol><li>Vés al curs desitjat</li><li>Fes clic a <strong>"Materials"</strong></li><li>Prem <strong>"Pujar Material"</strong></li><li>Selecciona l\'arxiu</li><li>Afegeix descripció</li><li>Puja l\'arxiu</li></ol>',
                'area' => 'materials',
                'context' => 'admin.courses.materials',
                'type' => 'procediment',
                'status' => 'validated',
                'order' => 1,
                'created_by' => 1,
            ],
            
            // Configuració
            [
                'title' => 'Com configurar el teu perfil',
                'slug' => 'com-configurar-perfil',
                'content' => '<h3>Per configurar el teu perfil:</h3><ol><li>Vés a <strong>"El Meu Perfil"</strong></li><li>Edita la teva informació personal</li><li>Puja la teva foto</li><li>Actualitza les teves dades</li><li>Guarda els canvis</li></ol>',
                'area' => 'configuracio',
                'context' => 'profile.edit',
                'type' => 'guia',
                'status' => 'validated',
                'order' => 1,
                'created_by' => 1,
            ],
            
            // Sistema d\'Ajuda
            [
                'title' => 'Com crear un article d\'ajuda',
                'slug' => 'com-crear-article-ajuda',
                'content' => '<h2>Per crear un nou article d\'ajuda:</h2><ol><li><strong>Accedeix al sistema d\'ajuda</strong><br>Vés a <code>/campus/help</code> i fes clic a "Nou Article"</li><li><strong>Completa la informació bàsica</strong><br><ul><li><strong>Títol:</strong> Títol clar i descriptiu</li><li><strong>Àrea:</strong> Selecciona l\'àrea (Cursos, Matrícula, Materials, Configuració)</li><li><strong>Context:</strong> On s\'aplica aquest article (opcional)</li><li><strong>Tipus:</strong> Tipus d\'article (opcional)</li><li><strong>Categoria:</strong> Selecciona una categoria existent (opcional)</li><li><strong>Estat:</strong> Esborrany, Validat o Obsolet</li><li><strong>Ordre:</strong> Ordre de visualització (opcional)</li></ul></li><li><strong>Escriu el contingut</strong><br>Utilitza l\'editor WYSIWYG per crear el teu article:<br><ul><li><strong>Format de text:</strong> Negreta, cursiva, subratllat</li><li><strong>Llistes:</strong> Numerades i amb vinyetes</li><li><strong>Enllaços:</strong> Per vincular a altres pàgines</li><li><strong>Imatges:</strong> Per afegir captures de pantalla</li><li><strong>Taules:</strong> Per organitzar informació</li><li><strong>Codi:</strong> Per mostrar fragments de codi</li></ul></li><li><strong>Revisa i publica</strong><br><ul><li>Revisa el contingut per detectar errors</li><li>Canvia l\'estat a "Validat" per publicar</li><li>Guarda l\'article</li></ul></li><li><strong>Consells addicionals</strong><br><ul><li>Utilitza títols clars i descriptius</li><li>Inclou captures de pantalla quan sigui possible</li><li>Estructura el contingut amb encapçalaments</li><li>Revisa l\'ortografia i la gramàtica</li><li>Afegeix exemples pràctics</li></ul></li></ol><h3>📌 Millors pràctiques</h3><ul><li>Utilitza un llenguatge clar i senzill</li><li>Divideix el contingut en seccions fàcils de llegir</li><li>Inclou exemples concrets del sistema</li><li>Mantingues el contingut actualitzat</li><li>Utilitza format consistent</li></ul><h3>🔧 Recursos addicionals</h3><ul><li><strong>Guia d\'estil:</strong> Consulta les directrius d\'estil del campus</li><li><strong>Plantilles:</strong> Utilitza les plantilles predefinides</li><li><strong>Suport:</strong> Contacta l\'equip tècnic si necessites ajuda</li></ul>',
                'area' => 'configuracio',
                'context' => 'admin.help.articles.create',
                'type' => 'guia',
                'status' => 'validated',
                'order' => 1,
                'created_by' => 1,
            ],
        ];

        foreach ($articles as $article) {
            HelpArticle::firstOrCreate([
                'title' => $article['title'],
                'slug' => $article['slug']
            ], $article);
        }

        $this->command->info('✅ Sistema d\'ajuda clàssic creat correctament');
    }
}
