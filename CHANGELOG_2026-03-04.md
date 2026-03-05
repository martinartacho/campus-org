# 📋 CHANGELOG - 4 Març 2026

## 🚀 Versió 2.1.0 - Release Major

### ✨ Noves Funcionalitats

#### 🎓 Sistema d'Importació de Cursos
- **Importació CSV amb professors** - Sistema complet per importar cursos i professors
- **Creació automàtica d'usuaris** - Generació automàtica d'usuaris i professors a partir del CSV
- **Template CSV** - Plantilla descarregable amb format correcte
- **Validació de dades** - Verificació de camps obligatoris i opcional
- **Report d'importació** - Informe detallat de resultats i errors

#### 📅 Sistema de Temporades Jeràrquic
- **Estructura jeràrquica** - Any acadèmic → Semestre → Trimestre
- **Dashboard interactiu** - Component per selecció visual de temporades
- **Calendar selector** - Selector amb lògica jeràrquica
- **Gestió acadèmica** - Control complet de cicles acadèmics
- **Migració automàtica** - Adaptació de cursos existents

#### 📚 Sistema d'Ajuda Complet
- **Articles d'ajuda** - Sistema complet de documentació
- **Categories per àrees** - Organització per cursos, matrícula, materials
- **Dashboard d'administració** - Panell de control amb estadístiques
- **Botó flotant d'ajuda** - Accés ràpid a ajuda contextual
- **API RESTful** - Endpoints per consulta d'ajuda

### 🎨 Millores d'Interfície

#### 📊 Taula de Cursos Optimitzada
- **Títols truncats** - Limitació a 200px amb 2 files de 100 caràcters
- **Dates combinades** - Data inici i fi en una mateixa columna
- **Estats amb icones** - Icones per actiu/inactiu i públic/privat
- **Scroll horitzontal** - Navegació optimitzada per taules amples
- **Tooltips** - Informació completa en hover

#### 🎨 Components UI
- **Dashboard Season Card** - Component reutilitzable per temporades
- **Botó d'ajuda flotant** - Disseny minimalista i accessible
- **Estils consistents** - Normalització de h4 i elements de dashboard

### 🛠️ Actualitzacions Tècniques

#### 🗄️ Base de Dades
```sql
-- Noves migracions
2026_03_03_000001_add_hierarchy_to_campus_seasons
2026_03_03_000002_create_campus_course_student_table
2026_03_03_000003_add_season_id_to_campus_registrations
```

#### 📦 Dependències
- **Vue.js 3.5.29** - Framework JavaScript
- **@vitejs/plugin-vue 6.0.4** - Plugin Vue per Vite
- **SweetAlert2 11.6.13** - Alertes personalitzades
- **Bootstrap Icons** - Icones per UI

#### 🎯 Models nous
- **CampusCourseStudent** - Relació curs-estudiant
- **HelpArticle** - Articles d'ajuda
- **HelpCategory** - Categories d'ajuda
- **HelpTag** - Tags per articles

### 🔧 Controllers nous
- **CampusImportController** - Importació CSV
- **HelpDashboardController** - Dashboard ajuda
- **HelpArticleController** - CRUD articles
- **HelpCategoryController** - CRUD categories

### 📁 Vistes noves
- **campus/courses/import** - Formulari d'importació
- **campus/help/** - Sistema complet d'ajuda
- **admin/help/** - Administració d'ajuda
- **components/dashboard-season-card** - Component reutilitzable

### 🌐 API noves
```php
// Rutes d'ajuda
GET /help/contextual     // Ajuda contextual
GET /help/areas         // Àrees d'ajuda
GET /help/search        // Cerca d'ajuda
GET /help/area/{area}   // Ajuda per àrea
GET /help/{slug}        // Article específic
```

---

## 🐛 Correccions

### 🔍 Errors resolts
- **Conflictes de merge** - Resolució manual de conflictes Git
- **Sintaxi PHP** - Correcció d'errors a routes/api.php
- **Assets compilats** - Rebuild correcte de CSS/JS
- **Cache netejada** - Optimització de cache i sessions

### 🎯 Millores d'estabilitat
- **Validació CSV** - Millora en validació de fitxers
- **Gestió d'errors** - Millor report d'incidències
- **Performance** - Optimització de consultes
- **Seguretat** - Millora en permisos i validacions

---

## 📊 Estadístiques

### 📈 Canvis quantitatius
- **Fitxers modificats:** 23+
- **Línies afegides:** 2,706
- **Línies eliminades:** 102
- **Migracions noves:** 3
- **Seeders nous:** 3
- **Controllers nous:** 4
- **Models nous:** 3
- **Vistes noves:** 8+

### 🎯 Impacte funcional
- **Sistemes nous:** 3 complets
- **Funcionalitats:** 15+ noves
- **Pàgines noves:** 5+
- **Components UI:** 3+
- **API endpoints:** 5+

---

## 🔄 Compatibilitat

### ✅ Versions compatibles
- **PHP:** 8.1+
- **Laravel:** 10.x
- **MySQL:** 8.0+
- **Node.js:** 18+
- **Vue.js:** 3.5+

### 🔄 Migracions requerides
- **Obligatòries:** 3 migracions noves
- **Seeders:** 2 seeders recomanats
- **Assets:** Rebuild requerit
- **Cache:** Neteja requerida

---

## 🚀 Instal·lació/Actualització

### 📋 Passos per actualitzar
```bash
# 1. Actualitzar codi
git pull origin main

# 2. Executar migracions
php artisan migrate --force

# 3. Instal·lar dependències
npm install --legacy-peer-deps

# 4. Compilar assets
npm run build

# 5. Executar seeders
php artisan db:seed --class=HelpSystemSeeder
php artisan db:seed --class=TestSeasonHierarchySeeder

# 6. Netejar cache
php artisan optimize:clear
```

---

## 🎯 Pròxim Release (v2.2.0)

### 🔍 En desenvolupament
- **Formulari de suport públic** - Sistema de tiquets
- **Millores PDF tresoreria** - Generació avançada
- **Sistema de notificacions** - Alertes en temps real
- **Analytics dashboard** - Estadístiques avançades

---

## 📝 Notes de Release

### ⚠️ Importants
- **Backup requerit** abans de migracions
- **Test en dev** abans de producció
- **Revisar permisos** després d'actualitzar
- **Validar funcionalitats** clau

### 🎯 Recomanacions
- **Provar importació CSV** amb dades reals
- **Verificar temporades** jeràrquiques
- **Testar sistema ajuda** complet
- **Monitoritzar performance** post-actualització

---

**Release Manager:** Martin Artacho  
**Data Release:** 4 Març 2026  
**Versió:** 2.1.0  
**Estat:** ✅ PRODUCTION READY
