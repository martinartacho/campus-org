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
            <strong>Nota:</strong> Pots veure un exemple complet a <a href="/ejemplo-colores" target="_blank">/ejemplo-colores</a>
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
            <strong>URL:</strong> <a href="/ejemplo-colores" target="_blank">/ejemplo-colores</a>
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
                        <pre><code>http://formacio-org.test/ejemplo-colores</code></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Consell:</strong> Guarda aquesta pàgina com a referència ràpida per als colors del sistema.
        </div>';
    }
}
