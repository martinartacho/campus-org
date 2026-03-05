# 📝 NOTES TÈCNIQUES - 4 Març 2026

## 🔍 ANÀLISI TÈCNICA DELS CANVIS

### 🗄️ Base de Dades

#### 📊 Estructura de Temporades
```sql
-- Nova estructura jeràrquica
campus_seasons
├── id (PK)
├── parent_id (FK - self-reference)
├── name (VARCHAR)
├── slug (UNIQUE)
├── academic_year (VARCHAR)
├── type (ENUM: annual, semester, trimester)
├── semester_number (TINYINT)
├── registration_start/end (DATETIME)
├── season_start/end (DATETIME)
├── status (ENUM)
├── is_active/is_current/is_default (BOOLEAN)
└── timestamps
```

#### 🔗 Relacions Noves
```sql
-- Taula pivot cursos-estudiants
campus_course_student
├── course_id (FK)
├── student_id (FK)
├── enrollment_date (DATETIME)
├── status (ENUM)
└── metadata (JSON)

-- Camp afegit a registrations
campus_registrations.season_id (FK)
```

### 🎯 Models Laravel

#### 📋 CampusSeason (Ampliat)
```php
// Relacions jeràrquiques
public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
public function children() { return $this->hasMany(self::class, 'parent_id'); }
public function courses() { return $this->hasMany(CampusCourse::class); }

// Scopes útils
public function scopeAcademicYear($query) { ... }
public function scopeCurrent($query) { ... }
public function scopeActive($query) { ... }

// Mètodes jeràrquics
public function isAcademicYear() { ... }
public function isSemester() { ... }
public function getFirstSemester() { ... }
```

#### 📚 Sistema d'Ajuda
```php
// HelpArticle
public function category() { return $this->belongsTo(HelpCategory::class); }
public function roles() { return $this->belongsToMany(Role::class, 'help_article_role'); }
public function tags() { return $this->belongsToMany(HelpTag::class, 'help_article_tag'); }

// HelpCategory
public function articles() { return $this->hasMany(HelpArticle::class); }
public function scopeActive($query) { ... }
public function scopeByArea($query, $area) { ... }
```

### 🎮 Components Vue.js

#### 🔘 ClassicHelpButton.vue
```javascript
// Botó flotant d'ajuda
data() {
  return {
    isOpen: false,
    articles: [],
    loading: false
  }
},
methods: {
  toggleHelp() { ... },
  loadArticles() { ... },
  searchArticles(query) { ... }
}
```

#### 📄 ClassicHelpArticle.vue
```javascript
// Article d'ajuda interactiu
props: ['article', 'context'],
computed: {
  formattedContent() {
    return this.article.content.replace(/\n/g, '<br>')
  }
}
```

### 🛠️ Serveis Laravel

#### 📤 CampusImportService
```php
// Lògica d'importació CSV
public function importFromCSV($file, $seasonId)
{
  // 1. Validar CSV
  $validation = $this->validateCSV($file);
  
  // 2. Processar files
  foreach ($validation['data'] as $row) {
    // 3. Crear/actualitzar professor
    $teacher = $this->findOrCreateTeacher($row);
    
    // 4. Crear/actualitzar curs
    $course = $this->findOrCreateCourse($row, $seasonId);
    
    // 5. Assignar professor al curs
    $this->assignTeacherToCourse($teacher, $course);
  }
  
  return $this->generateReport();
}
```

### 🎨 Frontend Optimització

#### 📊 Taula de Cursos
```blade
<!-- Optimització de títols llargs -->
<td style="max-width: 200px; width: 200px;">
  <div class="space-y-1">
    <div class="truncate" title="{{ $course->title }}">
      {{ Str::limit($course->title, 100) }}
    </div>
    @if(strlen($course->title) > 100)
      <div class="text-xs text-gray-400 truncate" title="{{ $course->title }}">
        {{ Str::substr($course->title, 100, 100) }}
      </div>
    @endif
  </div>
</td>

<!-- Dates combinades -->
<td>
  <div class="max-w-xs">
    <div class="font-medium">{{ $course->start_date->format('d/m/Y') }}</div>
    <div class="text-xs text-gray-400">{{ $course->end_date->format('d/m/Y') }}</div>
  </div>
</td>

<!-- Estats amb icones -->
<td>
  <div class="flex items-center space-x-2">
    @if($course->is_active)
      <i class="bi bi-check-circle-fill text-green-500"></i>
      <span class="text-xs text-green-600">Active</span>
    @else
      <i class="bi bi-x-circle-fill text-red-500"></i>
      <span class="text-xs text-red-600">Inactive</span>
    @endif
    @if($course->is_public)
      <i class="bi bi-globe text-blue-500"></i>
    @else
      <i class="bi bi-lock text-gray-400"></i>
    @endif
  </div>
</td>
```

### 🌐 API RESTful

#### 📚 Endpoints d'Ajuda
```php
// Rutes públiques
Route::prefix('help')->group(function () {
    Route::get('/contextual', [HelpController::class, 'contextual']);
    Route::get('/areas', [HelpController::class, 'areas']);
    Route::get('/search', [HelpController::class, 'search']);
    Route::get('/area/{area}', [HelpController::class, 'byArea']);
    Route::get('/{slug}', [HelpController::class, 'show']);
});

// Rutes protegides
Route::middleware(['auth:sanctum', 'verified'])->prefix('/help')->group(function () {
    Route::get('/stats', [HelpController::class, 'stats']);
});
```

### 🔧 Configuració Vite

#### ⚡ Optimització de Build
```javascript
// vite.config.js
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue'],
                    bootstrap: ['bootstrap'],
                },
            },
        },
    },
});
```

### 🎯 Performance Consideracions

#### 📊 Optimitzacions Implementades
```php
// Càrrega eficient de cursos
$courses = CampusCourse::with(['season', 'category', 'teachers'])
    ->when($request->search_code, function($query, $code) {
        $query->where('code', 'like', "%{$code}%");
    })
    ->when($request->search_title, function($query, $title) {
        $query->where('title', 'like', "%{$title}%");
    })
    ->when($request->search_season, function($query, $seasonId) {
        $query->where('season_id', $seasonId);
    })
    ->orderBy('created_at', 'desc')
    ->paginate(15);
```

#### 🗄️ Índexs Recomanats
```sql
-- Índexs per optimitzar rendiment
CREATE INDEX idx_campus_courses_season_id ON campus_courses(season_id);
CREATE INDEX idx_campus_courses_code ON campus_courses(code);
CREATE INDEX idx_campus_courses_title ON campus_courses(title);
CREATE INDEX idx_help_articles_category_id ON help_articles(help_category_id);
CREATE INDEX idx_help_articles_area ON help_articles(area);
CREATE INDEX idx_help_articles_status ON help_articles(status);
```

### 🔍 Debug i Monitorització

#### 📊 Logs Implementats
```php
// ImportController - Log d'operacions
Log::info('CSV Import Started', [
    'user_id' => auth()->id(),
    'file_size' => $file->getSize(),
    'season_id' => $seasonId
]);

// SeasonController - Log de canvis
Log::info('Season set as current', [
    'season_id' => $season->id,
    'season_name' => $season->name,
    'user_id' => auth()->id()
]);
```

### 🛡️ Seguretat

#### 🔐 Validacions Implementades
```php
// Import CSV - Validació estricta
$rules = [
    'first_name' => 'required|string|max:255',
    'last_name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'code' => 'required|string|max:50|unique:campus_courses,code',
    'title' => 'required|string|max:255',
    'credits' => 'nullable|integer|min:0|max:999',
    'hours' => 'nullable|integer|min:0|max:9999',
    'price' => 'nullable|numeric|min:0|max:99999.99',
];

// Help System - Permisos
Gate::define('help.admin', function ($user) {
    return $user->hasRole('admin') || $user->hasPermissionTo('manage-help');
});
```

### 🔄 Cache Strategy

#### 📊 Cache Implementat
```php
// HelpController - Cache d'articles
public function index()
{
    $articles = Cache::remember('help.articles.index', 3600, function () {
        return HelpArticle::with(['category', 'tags'])
            ->byStatus('published')
            ->orderBy('order')
            ->get();
    });
}

// SeasonController - Cache de temporades
public function getCurrentSeason()
{
    return Cache::remember('current.season', 7200, function () {
        return CampusSeason::where('is_current', true)->first();
    });
}
```

---

## 🎯 Recomanacions Tècniques

### 📊 Monitorització
- **Query performance** - Monitoritzar consultes lentes
- **Cache hit ratio** - Verificar eficàcia del cache
- **Memory usage** - Controlar consum de memòria
- **API response times** - Mesurar latència

### 🔍 Manteniment
- **Database optimization** - OPTIMIZE TABLE periòdicament
- **Log rotation** - Configurar rotació de logs
- **Backup verification** - Verificar integritat de backups
- **Security audits** - Revisar permisos i vulnerabilitats

### 🚈 Escalabilitat
- **Horizontal scaling** - Preparar per load balancing
- **Database sharding** - Considerar per grans volums
- **CDN implementation** - Per assets estàtics
- **Queue system** - Per tasques asíncrones

---

## 📝 TODO Tècnic

### 🔍 Pending Improvements
- [ ] Implementar queue system per importacions grans
- [ ] Afegir rate limiting a API d'ajuda
- [ ] Optimitzar queries N+1 problemes
- [ ] Implementar full-text search per ajuda
- [ ] Afegir monitoring en temps real

### 🎯 Technical Debt
- [ ] Refactoritzar CampusImportService
- [ ] Separar lògica de UI de controllers
- [ ] Implementar repository pattern
- [ ] Afegir unit tests
- [ ] Documentar API amb Swagger

---

**Data:** 4 Març 2026  
**Autor:** Martin Artacho  
**Versió:** 2.1.0  
**Estat:** ✅ PRODUCTION READY
