````markdown
# 🚀 Puesta en marcha de `campus.upg.cat` (Laravel 12)

Este documento describe paso a paso cómo desplegar el entorno de desarrollo `campus.upg.cat` en un VPS con Ubuntu 24.04 y Apache, usando el repositorio [martinartacho/campus-org ](https://github.com/martinartacho/campus-org ). Incluye las incidencias encontradas y sus soluciones.

---
ATENCIÓ

Eliminar TODO el contenido interno

Salvo la carpeta /var/www/campus.upg.cat

Para mantener el VirtualHost intacto

Ejecuta:

sudo rm -rf /var/www/campus.upg.cat/*
sudo rm -rf /var/www/campus.upg.cat/.[!.]*

Esto elimina:

Archivos normales

Archivos ocultos (incluido .git)

Pero mantiene la carpeta principal

## ✅ 1. Clonar el repositorio

```bash
cd /var/www/campus.upg.cat
sudo git clone https://github.com/martinartacho/campus-org.git .
````

---

## ✅ 2. Instalar dependencias de Laravel

```bash
composer install --no-dev
cp .env.example .env
php artisan key:generate
```

---

## ✅ 3. Crear la base de datos en MySQL

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE campus_upg CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'usuari'@'localhost' IDENTIFIED BY 'contraseña_segura';
GRANT ALL PRIVILEGES ON campus_upg.* TO 'usuari'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## ✅ 4. Configurar el archivo `.env`

```env
APP_NAME=CampusUPG
APP_ENV=local
APP_URL=http://campus.upg.cat

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campus_upg
DB_USERNAME=usuari
DB_PASSWORD=contraseña_segura
```

---

## ✅ 5. Establecer permisos correctos

```bash
cd /var/www/campus.upg.cat
sudo chown -R $USER:www-data .
sudo chmod -R 775 storage bootstrap/cache
```

---

## ✅ 6. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

Esto creará usuarios de ejemplo con contraseñas públicas, por seguridad cambia las contraseñas (indicaciones mas abajo) 
Para el caso de campus.upg.cat




---

## ⚠️ 7. Incidencias encontradas y soluciones

### ❗ `Permission denied` en `storage/logs/laravel.log`

```bash
sudo chown -R $USER:www-data .
sudo chmod -R 775 storage bootstrap/cache
```

---

### ❗ `Vite manifest not found`

Falta compilar los assets frontend:

```bash
npm install
npm run build
```

---

### ❗ Cambiar las contraseñas de todos los usuarios

**Command:**

```bash
php artisan users:change-all-passwords
```

users:change-all-passwords

**Tinker:** usar Tinker para cambiar todas las contraseñas:

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::all()->each(function ($user) {
    $user->password = Hash::make('Password.Seguro!');
    $user->save();
});
exit
```

---

## 🔒 Seguridad recomendada

* Cambiar cualquier contraseña que esté en el seeder (generadas aleatorimamente, etc).
* Nunca subir archivos `.env` al repositorio.
* Regenerar claves y tokens si fueron publicados por error.

---

## ✅ 8. Recargar Apache y probar

```bash
sudo systemctl reload apache2
```

Abrir en el navegador:

```
https://campus.upg.cat
```

---

## 📌 Pendiente

* Revisar registro de comandos personalizados en Laravel 11.
* Implementar variante del comando para generar contraseñas aleatorias por usuario (opcional).
* Automatizar este proceso en un script o ansible si se quiere escalar a staging/producción.

---

```

---

¿Quieres que genere este archivo directamente y lo copie en el servidor usando un script Bash o que te lo prepare también en `.txt` para descargar desde la web?
```

---

## Autor
Artacho DevOps ✨
