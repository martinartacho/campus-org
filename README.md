# Formacio-Org · Campus Virtual

Plataforma de gestió educativa desenvolupada amb **Laravel**, pensada per a organitzacions de formació no reglada.  
Permet gestionar cursos, usuaris, rols, inscripcions i comunicació mitjançant un sistema multi-rol amb permisos.

---

## 🚨 **ADVERTÈNCIA CRÍTICA DE SEGURETAT** 🚨

### **⚠️ NO CAMVIAR MAI L'APP_KEY SENSE BACKUP**

```bash
# ❌ PERILL MORTAL - NO FER AIXÒ
APP_KEY=base64:clau_antiga
APP_KEY=base64:clau_nova  # 💥 PERDA TOTAL DE DADES
```

**Conseqüències del canvi d'APP_KEY:**
- 💥 **PÈRDUA PERMANENT** de totes les dades bancàries encriptades
- 💥 **Error "The payload is invalid"** a tot el sistema
- 💥 **Teachers no poden accedir** al dashboard
- 💥 **Dades personals sensibles** es corrompen irreversiblement

### **🔐 SISTEMA D'ENCRYPTACIÓ DE DADES BANCÀRIES**

**Implementació:**
- ✅ **AES-256-CBC** amb Laravel Crypt
- ✅ **Dades sensibles** totalment xifrades a BD
- ✅ **Masking automàtic** per visualització segura
- ✅ **Validació IBAN** espanyol integrada
- ✅ **Migració automàtica** de dades antigues

**Camps xifrats:**
- `iban` - IBAN bancari
- `bank_titular` - Titular del compte
- `fiscal_id` - Identificació fiscal
- `beneficiary_iban` - IBAN beneficiari
- `beneficiary_titular` - Titular beneficiari
- `beneficiary_dni` - DNI beneficiari

### **🔄 PROCEDIMENT DE MIGRACIÓ**

```bash
# ✅ Migració segura de dades antigues
php artisan banking:migrate-all-to-encryption --force

# ✅ Recuperació de dades corruptes
php artisan banking:recover --fix
```

### **📋 COMPROVACIÓ DE SEGURETAT**

Abans de qualsevol desplegament o canvi:
```bash
# 1. Verificar estat d'encriptació
php artisan banking:migrate-all-to-encryption --dry-run

# 2. Comprovar integritat de dades
php artisan banking:recover --check

# 3. Test de desencriptació
php artisan tinker --execute="echo auth()->user()->teacherProfile->decrypted_iban;"
```

---

## ✨ Característiques principals

- Autenticació i autorització amb **Spatie Roles & Permissions**
- Dashboard multi-rol
- Gestió d’usuaris, perfils i permisos
- Components Blade reutilitzables
- Tailwind CSS amb colors semàntics
- API REST amb autenticació JWT

---

## 👥 Rols del sistema

- **admin** → Administració completa
- **gestor / editor** → Gestió parcial de continguts
- **teacher** → Funcions docents
- **student** → Accés a cursos i continguts
- **user** → Accés bàsic
- **invitado** → Accés limitat

---

## 🧩 Components UI

### Botó reutilitzable

**Fitxer:**  
`resources/views/components/campus-button.blade.php`

**Variants:** `header`, `primary`, `secondary`, `danger`

```blade
<x-campus-button type="submit" variant="primary">
    Desar
</x-campus-button>
🗺️ Rutes principals
Públiques
/ → Pàgina de benvinguda

Autenticació
Login, registre i recuperació (definides a auth.php)

Protegides
/dashboard → Panell principal

/profile → Gestió del perfil

Administració (rol admin)
/admin/users → CRUD d’usuaris

🧠 Arquitectura
Controladors
DashboardController

ProfileController

Admin\*Controller

Api\*Controller

Middlewares
auth

verified

role

🛠️ Instal·lació
1. Clonar el repositori
bash
Copia el codi
git clone https://github.com/martinartacho/formacio-org.git
cd formacio-org
2. Instal·lar dependències
bash
Copia el codi
composer install
npm install
npm run dev
3. Configurar entorn
bash
Copia el codi
cp .env.example .env
php artisan key:generate
Configura la base de dades a .env.

4. Migracions i dades
bash
Copia el codi
php artisan migrate
php artisan db:seed
(opcional)

bash
Copia el codi
crea directori i copia archiu
mkdir storage\app\imports

php artisan db:seed --class=SettingSeeder
▶️ Execució
bash
Copia el codi
php artisan serve
Accedeix a:
👉 http://localhost:8000

🎨 Uso de COMPLETA A TAILWIND CSS 

Tailwind (colors semàntics) 
Exemple d’ús:

html
Copia el codi
<span class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded-full">
    {{ $event->answers_count }}
</span>
Després de modificar tailwind.config.js:

Podem utilitzar botons reutilitzables estan ubicats en:
resources/views/components/campus-button.blade.php
o fer servir bi bi-trash-fill (Bootstrap Icons) 
<i class="bi bi-trash-fill mr-1"></i>Eliminar
 No usar fas fa-edit (Font Awesome).
 <i class="fas fa-trash mr-1"></i>Eliminar


bash
Copia el codi
npm run dev
# o
npm run build
🤝 Contribució
Fork del repositori

Crear branca

bash
Copia el codi
git checkout -b nova-funcionalitat
Commit i push

Pull Request a GitHub

📄 Llicència
Llicència MIT. Vegeu l’arxiu LICENSE.

✍️ Autor
Hartacho Team

yaml
Copia el codi

---

# 📄 API.md

```md
# API · Formacio-Org

API REST desenvolupada amb **Laravel** i autenticació **JWT**.

📍 Endpoint base:
https://nomdomini/api

yaml
Copia el codi

---

## 🔐 Autenticació

### Login

```http
POST /api/login
json
Copia el codi
{
  "email": "usuari@exemple.com",
  "password": "Pass.Seg.123"
}
Resposta:

json
Copia el codi
{
  "access_token": "jwt_token",
  "token_type": "bearer",
  "expires_in": 3600
}
👤 Perfil d’usuari
Usuari autenticat
http
Copia el codi
GET /api/me
Header:

css
Copia el codi
Authorization: Bearer {token}
Actualitzar perfil
http
Copia el codi
PUT /api/profile
json
Copia el codi
{
  "name": "Nou Nom",
  "email": "nou@email.com"
}
🔒 Seguretat
Canviar contrasenya
http
Copia el codi
PUT /api/change-password
json
Copia el codi
{
  "current_password": "anterior",
  "new_password": "nova"
}
Eliminar compte
http
Copia el codi
DELETE /api/delete-account
🔔 Notificacions
Guardar token FCM
http
Copia el codi
POST /api/save-fcm-token
json
Copia el codi
{
  "fcm_token": "firebase_token"
}
🧪 Debug / Logging
http
Copia el codi
GET /api/test-log
Genera un warning a storage/logs/laravel.log.

ℹ️ Notes
Les rutes protegides requereixen JWT a la capçalera Authorization

No utilitzar curl -k excepte en entorns de proves

Tokens amb expiració configurable

yaml
Copia el codi

---

## ✅ Resultat final al repositori

formacio-org/
├── README.md
├── API.md
├── app/
├── resources/
├── routes/
└── ...

yaml
Copia el codi

---