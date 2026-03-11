<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\User;

class HelpArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el usuario administrador para created_by
        $adminUser = User::where('email', 'campus@upg.test')->first();
        if (!$adminUser) {
            $adminUser = User::where('email', 'like', '%admin%')->first();
        }
        
        // Obtener categoría de configuración o crearla
        $configCategory = HelpCategory::where('name', 'Configuració')->first();
        if (!$configCategory) {
            $configCategory = HelpCategory::create([
                'name' => 'Configuració',
                'area' => 'configuracio',
                'icon' => 'bi-gear',
                'order' => 10,
                'is_active' => true,
            ]);
        }

        // Artículo principal sobre colores del sistema manager
        HelpArticle::firstOrCreate([
            'slug' => 'colors-sistema-manager'
        ], [
            'title' => 'Colors del Sistema Manager',
            'area' => 'configuracio',
            'context' => 'diseny',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 1,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getColorsManagerContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        // Artículo sobre uso de colores en badges
        HelpArticle::firstOrCreate([
            'slug' => 'us-colors-badges'
        ], [
            'title' => 'Ús de Colors en Badges',
            'area' => 'configuracio',
            'context' => 'diseny',
            'type' => 'exemple',
            'status' => 'validated',
            'order' => 2,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getBadgesContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        // Artículo sobre componente campus-button
        HelpArticle::firstOrCreate([
            'slug' => 'component-campus-button'
        ], [
            'title' => 'Component Campus Button amb Colors Personalitzats',
            'area' => 'configuracio',
            'context' => 'desenvolupament',
            'type' => 'component',
            'status' => 'validated',
            'order' => 3,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getCampusButtonContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        // Artículo sobre ejemplo práctico
        HelpArticle::firstOrCreate([
            'slug' => 'exemple-practic-colors'
        ], [
            'title' => 'Exemple Pràctic: Pàgina de Colors',
            'area' => 'configuracio',
            'context' => 'demo',
            'type' => 'exemple',
            'status' => 'validated',
            'order' => 4,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getExampleContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        // Artículo con el ejemplo completo de colores
        HelpArticle::firstOrCreate([
            'slug' => 'exemple-complet-colors'
        ], [
            'title' => 'Exemple Complet: Colors del Sistema Manager',
            'area' => 'configuracio',
            'context' => 'demo',
            'type' => 'exemple',
            'status' => 'validated',
            'order' => 5,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getCompleteExampleContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        // Artículo sobre roles y permisos del sistema
        HelpArticle::firstOrCreate([
            'slug' => 'rols-permisos-sistema'
        ], [
            'title' => 'Rols i Permisos del Sistema',
            'area' => 'super-admin',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 1,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getRolesPermisosContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        // Artículos específicos para cada rol
        HelpArticle::firstOrCreate([
            'slug' => 'rol-super-admin'
        ], [
            'title' => 'Super Admin - Control Total',
            'area' => 'super-admin',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 2,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getSuperAdminContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        HelpArticle::firstOrCreate([
            'slug' => 'rol-admin'
        ], [
            'title' => 'Admin - Administració Completa',
            'area' => 'admin',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 3,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getAdminContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        HelpArticle::firstOrCreate([
            'slug' => 'rol-director'
        ], [
            'title' => 'Director - Direcció Acadèmica',
            'area' => 'director',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 4,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getDirectorContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        HelpArticle::firstOrCreate([
            'slug' => 'rol-manager'
        ], [
            'title' => 'Manager - Coordinació General',
            'area' => 'manager',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 5,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getManagerContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        HelpArticle::firstOrCreate([
            'slug' => 'rol-comunicacio'
        ], [
            'title' => 'Comunicació - Notificacions i Esdeveniments',
            'area' => 'comunicacio',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 6,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getComunicacioContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        HelpArticle::firstOrCreate([
            'slug' => 'rol-coordinacio'
        ], [
            'title' => 'Coordinació - Gestió Acadèmica',
            'area' => 'coordinacio',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 7,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getCoordinacioContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        HelpArticle::firstOrCreate([
            'slug' => 'rol-secretaria'
        ], [
            'title' => 'Secretaria - Gestió Administrativa',
            'area' => 'secretaria',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 8,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getSecretariaContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        HelpArticle::firstOrCreate([
            'slug' => 'rol-gestio'
        ], [
            'title' => 'Gestió - Operativa del Campus',
            'area' => 'gestio',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 9,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getGestioContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        HelpArticle::firstOrCreate([
            'slug' => 'rol-teacher'
        ], [
            'title' => 'Professor/a - Docència i Cursos',
            'area' => 'teacher',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 10,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getTeacherContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);

        HelpArticle::firstOrCreate([
            'slug' => 'rol-student'
        ], [
            'title' => 'Estudiant - Accés i Curs',
            'area' => 'student',
            'context' => 'sistema',
            'type' => 'guia',
            'status' => 'validated',
            'order' => 11,
            'help_category_id' => $configCategory->id,
            'version' => '1.0',
            'content' => $this->getStudentContent(),
            'created_by' => $adminUser->id ?? 1,
        ]);
    }

    private function getColorsManagerContent(): string
    {
        return '<h2>Colors del Sistema Manager</h2>
        
        <p>El sistema manager disposa d\'una paleta de colors personalitzada per identificar visualment cada rol i les seves responsabilitats.</p>
        
        <h3>Paleta de Colors</h3>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <span class="badge bg-manager-600 text-white mb-2">Manager</span>
                        <p><strong>Manager:</strong> Purple (<code>bg-manager-600</code>)</p>
                        <p>Rol principal de coordinació general de gestió.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <span class="badge bg-comunicacio-500 text-white mb-2">Comunicació</span>
                        <p><strong>Comunicació:</strong> Teal (<code>bg-comunicacio-500</code>)</p>
                        <p>Gestió de comunicació i edició de contingut.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <span class="badge bg-coordinacio-500 text-white mb-2">Coordinació</span>
                        <p><strong>Coordinació:</strong> Indigo (<code>bg-coordinacio-500</code>)</p>
                        <p>Coordinació acadèmica i gestió de cursos.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <span class="badge bg-gestio-500 text-white mb-2">Gestió</span>
                        <p><strong>Gestió:</strong> Orange (<code>bg-gestio-500</code>)</p>
                        <p>Gestió operativa del campus.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <h3>Configuració Tècnica</h3>
        
        <p>Els colors estan definits a <code>tailwind.config.js</code> amb totes les variacions de 50 a 900:</p>
        
        <pre><code class="language-javascript">// Exemple de configuració
colors: {
    manager: {
        50: \'#faf5ff\',
        100: \'#f3e8ff\',
        // ...
        600: \'#9333ea\',  // Color principal
        // ...
        900: \'#581c87\',
    }
}</code></pre>
        
        <h3>Estats Automàtics</h3>
        
        <p>Cada color disposa d\'estats automàtics:</p>
        <ul>
            <li><strong>Hover:</strong> <code>hover:bg-{color}-{shade}</code></li>
            <li><strong>Focus:</strong> <code>focus:ring-{color}</code></li>
            <li><strong>Active:</strong> <code>active:bg-{color}-{shade}</code></li>
        </ul>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Nota:</strong> Pots veure un exemple complet a <a href="/help/exemple-complet-colors" target="_blank">Exemple Complet: Colors del Sistema Manager</a>
        </div>';
    }

    private function getBadgesContent(): string
    {
        return '<h2>Ús de Colors en Badges</h2>
        
        <p>Els badges són perfectes per mostrar visualment els rols dels usuaris i altres informacions categòriques.</p>
        
        <h3>Badges Bàsics</h3>
        
        <p>Utilitza les classes directes de Tailwind:</p>
        
        <pre><code class="language-html"><!-- Badges de rols -->
&lt;span class="bg-manager-600 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Manager
&lt;/span&gt;

&lt;span class="bg-comunicacio-500 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Comunicació
&lt;/span&gt;

&lt;span class="bg-coordinacio-500 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Coordinació
&lt;/span&gt;

&lt;span class="bg-gestio-500 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Gestió
&lt;/span&gt;</code></pre>
        
        <h3>Classes Recomanades</h3>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Propietat</th>
                    <th>Classe Recomanada</th>
                    <th>Descripció</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Fons</td>
                    <td><code>bg-{color}-{shade}</code></td>
                    <td>Color de fons del badge</td>
                </tr>
                <tr>
                    <td>Text</td>
                    <td><code>text-white</code></td>
                    <td>Text blanc per contrast</td>
                </tr>
                <tr>
                    <td>Mida</td>
                    <td><code>text-xs</code></td>
                    <td>Text petit per badges</td>
                </tr>
                <tr>
                    <td>Font</td>
                    <td><code>font-bold</code></td>
                    <td>Text en negreta</td>
                </tr>
                <tr>
                    <td>Padding</td>
                    <td><code>px-2 py-1</code></td>
                    <td>Espaiat intern</td>
                </tr>
                <tr>
                    <td>Forma</td>
                    <td><code>rounded-full</code></td>
                    <td>Badges arrodonits</td>
                </tr>
            </tbody>
        </table>';
    }

    private function getCampusButtonContent(): string
    {
        return '<h2>Component Campus Button amb Colors Personalitzats</h2>
        
        <p>El component <code>campus-button</code> suporta variants personalitzades per als colors del sistema manager.</p>
        
        <h3>Ús Bàsic</h3>
        
        <p>Utilitza la variant específica per cada rol:</p>
        
        <pre><code class="language-html"><!-- Botó Manager -->
&lt;x-campus-button variant="manager" href="#"&gt;
    Accés Manager
&lt;/x-campus-button&gt;

<!-- Botó Comunicació -->
&lt;x-campus-button variant="comunicacio" href="#"&gt;
    Comunicació
&lt;/x-campus-button&gt;

<!-- Botó Coordinació -->
&lt;x-campus-button variant="coordinacio" href="#"&gt;
    Coordinació
&lt;/x-campus-button&gt;

<!-- Botó Gestió -->
&lt;x-campus-button variant="gestio" href="#"&gt;
    Gestió
&lt;/x-campus-button&gt;</code></pre>
        
        <h3>Característiques Automàtiques</h3>
        
        <p>Cada variant inclou:</p>
        <ul>
            <li><strong>Color de fons:</strong> Color principal del rol</li>
            <li><strong>Text blanc:</strong> Per màxim contrast</li>
            <li><strong>Hover state:</strong> Color més fosc al passar el ratolí</li>
            <li><strong>Focus state:</strong> Anell de focus del color del rol</li>
            <li><strong>Active state:</strong> Color encara més fons al fer clic</li>
        </ul>';
    }

    private function getExampleContent(): string
    {
        return '<h2>Exemple Pràctic: Pàgina de Colors</h2>
        
        <p>Pots veure un exemple complet de tots els colors i els seus usos a la pàgina d\'exemple:</p>
        
        <div class="alert alert-primary">
            <i class="bi bi-link-45deg me-2"></i>
            <strong>URL:</strong> <a href="/help/exemple-complet-colors" target="_blank">/help/exemple-complet-colors</a>
        </div>
        
        <h3>Com Accedir-hi</h3>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Desenvolupament Local</h5>
                    </div>
                    <div class="card-body">
                        <p>Si estàs treballant en local:</p>
                        <pre><code>http://localhost:8000/ejemplo-colores</code></pre>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Entorn de Producció</h5>
                    </div>
                    <div class="card-body">
                        <p>En el teu domini:</p>
                        <pre><code>http://formacio-org.test/help/exemple-complet-colors</code></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Consell:</strong> Guarda aquesta pàgina com a referència ràpida per als colors del sistema.
        </div>';
    }

    private function getCompleteExampleContent(): string
    {
        return '<h2>Exemple Complet: Colors del Sistema Manager</h2>
        
        <p>A continuació pots veure un exemple complet i funcional de tots els colors del sistema manager amb el seu codi per copiar i enganxar.</p>
        
        <h3>Badges de Rols</h3>
        
        <div class="flex flex-wrap gap-2 mb-6">
            <span class="bg-manager-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                Manager
            </span>
            
            <span class="bg-comunicacio-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Comunicació
            </span>
            
            <span class="bg-coordinacio-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Coordinació
            </span>
            
            <span class="bg-gestio-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Gestió
            </span>
        </div>
        
        <h4>Codi per Badges</h4>
        
        <pre><code class="language-html">&lt;!-- Badges de rols --&gt;
&lt;span class="bg-manager-600 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Manager
&lt;/span&gt;

&lt;span class="bg-comunicacio-500 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Comunicació
&lt;/span&gt;

&lt;span class="bg-coordinacio-500 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Coordinació
&lt;/span&gt;

&lt;span class="bg-gestio-500 text-white text-xs font-bold px-2 py-1 rounded-full"&gt;
    Gestió
&lt;/span&gt;</code></pre>
        
        <h3>Botons amb Colors Personalitzats</h3>
        
        <div class="flex flex-wrap gap-2 mb-6">
            <button class="bg-manager-600 hover:bg-manager-700 text-white px-4 py-2 rounded text-sm">
                Accés Manager
            </button>
            
            <button class="bg-comunicacio-500 hover:bg-comunicacio-600 text-white px-4 py-2 rounded text-sm">
                Comunicació
            </button>
            
            <button class="bg-coordinacio-500 hover:bg-coordinacio-600 text-white px-4 py-2 rounded text-sm">
                Coordinació
            </button>
            
            <button class="bg-gestio-500 hover:bg-gestio-600 text-white px-4 py-2 rounded text-sm">
                Gestió
            </button>
        </div>
        
        <h4>Codi per Botons</h4>
        
        <pre><code class="language-html">&lt;!-- Botons amb variants --&gt;
&lt;x-campus-button variant="manager" href="#"&gt;
    Accés Manager
&lt;/x-campus-button&gt;

&lt;x-campus-button variant="comunicacio" href="#"&gt;
    Comunicació
&lt;/x-campus-button&gt;

&lt;x-campus-button variant="coordinacio" href="#"&gt;
    Coordinació
&lt;/x-campus-button&gt;

&lt;x-campus-button variant="gestio" href="#"&gt;
    Gestió
&lt;/x-campus-button&gt;</code></pre>
        
        <h3>Estats Interactius</h3>
        
        <p>Els colors inclouen estats hover, focus i active automàtics:</p>
        
        <ul>
            <li><strong>Hover:</strong> <code class="bg-gray-100 px-2 py-1 rounded">hover:bg-{color}-{shade}</code></li>
            <li><strong>Focus:</strong> <code class="bg-gray-100 px-2 py-1 rounded">focus:ring-{color}</code></li>
            <li><strong>Active:</strong> <code class="bg-gray-100 px-2 py-1 rounded">active:bg-{color}-{shade}</code></li>
        </ul>
        
        <h3>Configuració Tècnica</h3>
        
        <p>Els colors estan definits a <code>tailwind.config.js</code> amb totes les variacions de 50 a 900:</p>
        
        <pre><code class="language-javascript">colors: {
    manager: {
        50: \'#faf5ff\',
        100: \'#f3e8ff\',
        200: \'#e9d5ff\',
        300: \'#d8b4fe\',
        400: \'#c084fc\',
        500: \'#a855f7\',
        600: \'#9333ea\',  // Color principal
        700: \'#7c3aed\',
        800: \'#6b21a8\',
        900: \'#581c87\',
    },
    comunicacio: {
        50: \'#f0fdfa\',
        100: \'#ccfbf1\',
        200: \'#99f6e4\',
        300: \'#5eead4\',
        400: \'#2dd4bf\',
        500: \'#14b8a6\',  // Color principal
        600: \'#0d9488\',
        700: \'#0f766e\',
        800: \'#115e59\',
        900: \'#134e4a\',
    },
    // ... altres colors
}</code></pre>
        
        <h3>Ús Recomanat</h3>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-blue-800 mb-2">
                <i class="bi bi-lightbulb mr-2"></i>Consells Pràctics
            </h4>
            <div class="text-blue-700">
                <ul class="list-disc list-inside space-y-2">
                    <li>Usa <code class="bg-blue-100 px-2 py-1 rounded">bg-{color}-{shade}</code> per al color de fons</li>
                    <li>Combina amb <code class="bg-blue-100 px-2 py-1 rounded">text-white</code> per màxim contrast</li>
                    <li>Afegeix <code class="bg-blue-100 px-2 py-1 rounded">hover:bg-{color}-{shade}</code> per efectes interactius</li>
                    <li>Usa <code class="bg-blue-100 px-2 py-1 rounded">focus:ring-{color}</code> per accessibilitat</li>
                </ul>
            </div>
        </div>';
    }

    private function getRolesPermisosContent(): string
    {
        return '<h2>Rols i Permisos del Sistema</h2>
        
        <p>El sistema disposa d\'una jerarquia de rols que defineix els permisos i accés a les diferents funcionalitats del campus.</p>
        
        <h3>Rols Principals</h3>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-purple-600">super-admin</h5>
                        <p class="card-text">Control total del sistema</p>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check text-success"></i> Accés complet a tot</li>
                            <li><i class="bi bi-check text-success"></i> Gestió d\'usuaris</li>
                            <li><i class="bi bi-check text-success"></i> Configuració global</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-danger">admin</h5>
                        <p class="card-text">Administrador complet</p>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check text-success"></i> Gestió administrativa</li>
                            <li><i class="bi bi-check text-success"></i> Contingut del campus</li>
                            <li><i class="bi bi-check text-success"></i> Usuaris i cursos</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-primary">director</h5>
                        <p class="card-text">Director del campus amb permisos complets</p>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check text-success"></i> Supervisió acadèmica</li>
                            <li><i class="bi bi-check text-success"></i> Gestió de professorat</li>
                            <li><i class="bi bi-check text-success"></i> Informes globals</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-purple-500">manager</h5>
                        <p class="card-text">Gestió coordinada de subroles</p>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check text-success"></i> Coordinació general</li>
                            <li><i class="bi bi-check text-success"></i> Supervisió d\'equips</li>
                            <li><i class="bi bi-check text-success"></i> Gestió integrada</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <h3>Subroles del Manager</h3>
        
        <p>El rol manager agrupa els següents subroles especialitzats:</p>
        
        <div class="table-responsive mb-4">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Subrol</th>
                        <th>Funció Principal</th>
                        <th>Responsabilitats</th>
                        <th>Color Identificatiu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge bg-manager-600">manager</span></td>
                        <td>Coordinació general de gestió</td>
                        <td>Supervisió global, coordinació d\'equips</td>
                        <td><span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">Purple</span></td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-comunicacio-500">comunicacio</span></td>
                        <td>Comunicació i edició</td>
                        <td>Notificacions, esdeveniments, comunicació</td>
                        <td><span class="bg-teal-100 text-teal-800 px-2 py-1 rounded">Teal</span></td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-coordinacio-500">coordinacio</span></td>
                        <td>Coordinació acadèmica</td>
                        <td>Cursos, estudiants, professorat</td>
                        <td><span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded">Indigo</span></td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-gestio-500">gestio</span></td>
                        <td>Gestió operativa</td>
                        <td>Operativa del campus, logística</td>
                        <td><span class="bg-orange-100 text-orange-800 px-2 py-1 rounded">Orange</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <h3>Altres Rols del Sistema</h3>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-warning">secretaria</h6>
                        <p class="card-text small">Gestió administrativa</p>
                        <ul class="small">
                            <li>Estudiants</li>
                            <li>Matrícules</li>
                            <li>Gestió admin</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-info">treasury</h6>
                        <p class="card-text small">Gestió financera</p>
                        <ul class="small">
                            <li>Pagaments</li>
                            <li>Factures</li>
                            <li>Informes econòmics</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-success">editor</h6>
                        <p class="card-text small">Edició de contingut</p>
                        <ul class="small">
                            <li>Materials</li>
                            <li>Documents</li>
                            <li>Contingut web</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-primary">teacher</h6>
                        <p class="card-text small">Professorat</p>
                        <ul class="small">
                            <li>Cursos assignats</li>
                            <li>Estudiants</li>
                            <li>Avaluacions</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-secondary">student</h6>
                        <p class="card-text small">Estudiant</p>
                        <ul class="small">
                            <li>Cursos matriculats</li>
                            <li>Materials</li>
                            <li>Progrés</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-light bg-dark rounded p-2 d-inline-block">user</h6>
                        <p class="card-text small">Usuari bàsic</p>
                        <ul class="small">
                            <li>Perfil personal</li>
                            <li>Informació general</li>
                            <li>Accés limitat</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <h3>Jerarquia de Permisos</h3>
        
        <div class="alert alert-info">
            <h5 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Jerarquia d\'Accés</h5>
            <p class="mb-2">Els rols superiors hereten tots els permisos dels rols inferiors:</p>
            <ol class="mb-0">
                <li><strong>super-admin</strong> - Control total</li>
                <li><strong>admin</strong> - Administració completa</li>
                <li><strong>director</strong> - Direcció acadèmica</li>
                <li><strong>manager</strong> - Coordinació general</li>
                <li><strong>subroles manager</strong> - Funcions especialitzades</li>
                <li><strong>altres rols</strong> - Accés específic segons funció</li>
            </ol>
        </div>
        
        <h3>Assignació d\'Usuaris</h3>
        
        <p>Els usuaris assignats als rols principals del sistema:</p>
        
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Rol</th>
                        <th>Email d\'Assignació</th>
                        <th>Funció Principal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge bg-purple-600">super-admin</span></td>
                        <td><code>superadmin@domain.com</code></td>
                        <td>Administració global</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-danger">admin</span></td>
                        <td><code>admin@domain.com</code></td>
                        <td>Administració del campus</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-primary">director</span></td>
                        <td><code>director@domain.com</code></td>
                        <td>Direcció acadèmica</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-manager-600">manager</span></td>
                        <td><code>manager@domain.com</code></td>
                        <td>Coordinació general</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-comunicacio-500">comunicacio</span></td>
                        <td><code>comunicacio@domain.com</code></td>
                        <td>Comunicació i edició</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-coordinacio-500">coordinacio</span></td>
                        <td><code>coordinacio@domain.com</code></td>
                        <td>Coordinació acadèmica</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-gestio-500">gestio</span></td>
                        <td><code>gestio@domain.com</code></td>
                        <td>Gestió operativa</td>
                    </tr>
                </tbody>
            </table>
        </div>';
    }

    private function getSuperAdminContent(): string
    {
        return '<h2>Super Admin - Control Total</h2>
        
        <p>El rol Super Admin té accés complet i il·limitat a tot el sistema.</p>
        
        <h3>Permisos Principals</h3>
        <ul>
            <li><strong>Control total:</strong> Accés a totes les funcionalitats</li>
            <li><strong>Gestió d\'usuaris:</strong> Crear, editar, eliminar usuaris</li>
            <li><strong>Configuració global:</strong> Tots els paràmetres del sistema</li>
            <li><strong>Seguretat:</strong> Configuració de permisos i rols</li>
        </ul>
        
        <h3>Responsabilitats</h3>
        <p>Com a Super Admin, ets responsable de:</p>
        <ul>
            <li>Administració completa del campus</li>
            <li>Gestió de tots els rols i permisos</li>
            <li>Manteniment i actualitzacions del sistema</li>
            <li>Seguretat i backups</li>
        </ul>';
    }

    private function getAdminContent(): string
    {
        return '<h2>Admin - Administració Completa</h2>
        
        <p>El rol Admin té accés complet a les funcions administratives del campus.</p>
        
        <h3>Permisos Principals</h3>
        <ul>
            <li><strong>Gestió administrativa:</strong> Accés a tot el panell d\'administració</li>
            <li><strong>Contingut:</strong> Gestió de cursos, materials, notificacions</li>
            <li><strong>Usuaris:</strong> Gestió de professorat, estudiants, personal</li>
            <li><strong>Informes:</strong> Accés a tots els informes del sistema</li>
        </ul>
        
        <h3>Responsabilitats</h3>
        <p>Com a Admin, ets responsable de:</p>
        <ul>
            <li>Coordinació general del campus</li>
            <li>Gestió de personal acadèmic</li>
            <li>Administració de cursos i matrícules</li>
            <li>Comunicació amb estudiants i professorat</li>
        </ul>';
    }

    private function getDirectorContent(): string
    {
        return '<h2>Director - Direcció Acadèmica</h2>
        
        <p>El rol Director té supervisió completa de les funcions acadèmiques del campus.</p>
        
        <h3>Permisos Principals</h3>
        <ul>
            <li><strong>Supervisió acadèmica:</strong> Tots els aspectes acadèmics</li>
            <li><strong>Gestió de professorat:</strong> Contractació, avaluació, formació</li>
            <li><strong>Cursos:</strong> Creació, modificació, eliminació de cursos</li>
            <li><strong>Informes globals:</strong> Estadístiques acadèmiques completes</li>
        </ul>
        
        <h3>Responsabilitats</h3>
        <p>Com a Director, ets responsable de:</p>
        <ul>
            <li>Direcció estratègica del campus</li>
            <li>Qualitat acadèmica</li>
            <li>Gestió del professorat</li>
            <li>Relacions institucionals</li>
        </ul>';
    }

    private function getManagerContent(): string
    {
        return '<h2>Manager - Coordinació General</h2>
        
        <p>El rol Manager coordina els diferents subròls especialitzats del sistema.</p>
        
        <h3>Permisos Principals</h3>
        <ul>
            <li><strong>Coordinació general:</strong> Supervisió de tots els subròls</li>
            <li><strong>Gestió integrada:</strong> Visió global de les operacions</li>
            <li><strong>Equip directiu:</strong> Gestió de l\'equip de coordinació</li>
            <li><strong>Informes executius:</strong> Seguiment de totes les àrees</li>
        </ul>
        
        <h3>Subròls Coordinats</h3>
        <ul>
            <li><strong>Comunicació:</strong> Notificacions, esdeveniments, comunicació</li>
            <li><strong>Coordinació:</strong> Cursos, estudiants, professorat</li>
            <li><strong>Secretaria:</strong> Estudiants, matrícules, gestió admin</li>
            <li><strong>Gestió:</strong> Operativa del campus, logística</li>
        </ul>';
    }

    private function getComunicacioContent(): string
    {
        return '<h2>Comunicació - Notificacions i Esdeveniments</h2>
        
        <p>El rol Comunicació gestiona tota la comunicació del campus.</p>
        
        <h3>Permisos Principals</h3>
        <ul>
            <li><strong>Notificacions:</strong> Enviament de missatges globals</li>
            <li><strong>Esdeveniments:</strong> Creació i gestió d\'esdeveniments</li>
            <li><strong>Comunicació:</strong> Butlletins, anuncis, circulars</li>
            <li><strong>Xarxes socials:</strong> Gestió de canals de comunicació</li>
        </ul>
        
        <h3>Responsabilitats</h3>
        <p>Com a responsable de Comunicació, ets responsable de:</p>
        <ul>
            <li>Mantenir informada la comunitat</li>
            <li>Coordinar campanyes de comunicació</li>
            <li>Gestionar les xarxes socials</li>
            <li>Crear contingut atractiu i efectiu</li>
        </ul>';
    }

    private function getCoordinacioContent(): string
    {
        return '<h2>Coordinació - Gestió Acadèmica</h2>
        
        <p>El rol Coordinació gestiona els aspectes acadèmics del campus.</p>
        
        <h3>Permisos Principals</h3>
        <ul>
            <li><strong>Gestió de cursos:</strong> Creació, edició, eliminació</li>
            <li><strong>Assignació de professorat:</strong> Cursos, horaris, aules</li>
            <li><strong>Gestió d\'estudiants:</strong> Matrícules, expedients</li>
            <li><strong>Horaris i aules:</strong> Planificació d\'espais i temps</li>
        </ul>
        
        <h3>Responsabilitats</h3>
        <p>Com a coordinador/a, ets responsable de:</p>
        <ul>
            <li>Qualitat acadèmica</li>
            <li>Coordinació del professorat</li>
            <li>Gestió de currículum</li>
            <li>Atenció a estudiants</li>
        </ul>';
    }

    private function getSecretariaContent(): string
    {
        return '<h2>Secretaria - Gestió Administrativa</h2>
        
        <p>El rol Secretaria gestiona els processos administratius del campus.</p>
        
        <h3>Permisos Principals</h3>
        <ul>
            <li><strong>Matrícules:</strong> Processament complet de matrícules</li>
            <li><strong>Expedients:</strong> Gestió d\'expedients acadèmics</li>
            <li><strong>Certificacions:</strong> Emissió de certificats i títols</li>
            <li><strong>Administració:</strong> Tràmits i gestió diària</li>
        </ul>
        
        <h3>Responsabilitats</h3>
        <p>Com a secretari/a, ets responsable de:</p>
        <ul>
            <li>Atenció a l\'usuari/a</li>
            <li>Gestió de matrícules i pagaments</li>
            <li>Manteniment d\'expedients</li>
            <li>Suport administratiu</li>
        </ul>';
    }

    private function getGestioContent(): string
    {
        return '<h2>Gestió - Operativa del Campus</h2>
        
        <p>El rol Gestió s\'encarrega de l\'operativa diària del campus.</p>
        
        <h3>Permisos Principals</h3>
        <ul>
            <li><strong>Operativa:</strong> Gestió de serveis generals</li>
            <li><strong>Instal·lacions:</strong> Manteniment d\'equipaments</li>
            <li><strong>Logística:</strong> Gestió de recursos materials</li>
            <li><strong>Seguretat:</strong> Control d\'accés i vigilància</li>
        </ul>
        
        <h3>Responsabilitats</h3>
        <p>Com a responsable de Gestió, ets responsable de:</p>
        <ul>
            <li>Funcionament del campus</li>
            <li>Manteniment d\'instal·lacions</li>
            <li>Gestió de serveis generals</li>
            <li>Coordinació de proveïdors</li>
        </ul>';
    }

    private function getTeacherContent(): string
    {
        return '<h2>Professor/a - Docència i Cursos</h2>
        
        <p>El rol Professor/a desenvolupa funcions docents al campus.</p>
        
        <h3>Permisos Principals</h3>
        <ul>
            <li><strong>Gestió de cursos:</strong> Cursos assignats</li>
            <li><strong>Estudiants:</strong> Seguiment acadèmic</li>
            <li><strong>Materials:</strong> Creació i gestió de contingut</li>
            <li><strong>Avaluacions:</strong> Qualificacions i seguiment</li>
        </ul>
        
        <h3>Responsabilitats</h3>
        <p>Com a professor/a, ets responsable de:</p>
        <ul>
            <li>Docència de les teves assignatures</li>
            <li>Avaluació del teu alumnat</li>
            <li>Manteniment del contingut del curs</li>
            <li>Atenció a estudiants</li>
        </ul>';
    }

    private function getStudentContent(): string
    {
        return '<h2>Estudiant - Accés i Curs</h2>
        
        <p>El rol Estudiant té accés a les funcions bàsiques d\'aprenentatge.</p>
        
        <h3>Permisos Principals</h3>
        <ul>
            <li><strong>Accés a cursos:</strong> Cursos matriculats</li>
            <li><strong>Materials:</strong> Descàrrega de contingut</li>
            <li><strong>Progrés:</strong> Seguiment del teu aprenentatge</li>
            <li><strong>Comunicació:</strong> Contacte amb el professorat</li>
        </ul>
        
        <h3>Responsabilitats</h3>
        <p>Com a estudiant, ets responsable de:</p>
        <ul>
            <li>El teu propi aprenentatge</li>
            <li>Cumplir amb els terminis del curs</li>
            <li>Participar activament</li>
            <li>Respectar les normes del campus</li>
        </ul>';
    }
}
