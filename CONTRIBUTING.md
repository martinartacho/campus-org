# Contribuir a MHartacho

Gracias por tu interés en contribuir a MHartacho. Este proyecto busca mantener un código modular, limpio y escalable, por lo que valoramos las contribuciones bien organizadas y alineadas con nuestra arquitectura.

---
# Estructura principal (raiz)
Se trata de la raíz del proyecto y contiene todo el sistema:
📦Proyecto
 ┣ 📂app
 ┣ 📂bootstrap
 ┣ 📂config
 ┣ 📂database
 ┣ 📂modules
 ┣ 📂public
 ┣ 📂resources
 ┣ 📂routes
 ┣ 📂storage
 ┣ 📂tests
 ┗ 📜composer.json, .env, artisan, etc.

# Estructura de módulos
Es un módulo funcional incluido dentro del sistema, que suele encargarse de:
Configuraciones (como user_settings)
Modelos compartidos
Servicios comunes (como UserSettingService)
Posiblemente lógica base reutilizable

🔹 Pero no es el proyecto raíz. No debería tener .env, ni artisan, ni composer.json propio.
Ejemplo de estructura de un módulo
📦modules
 ┣ 📂UserSettings
 ┃ ┣ 📂Database
 ┃ ┃ ┗ 📂Migrations
 ┃ ┃ ┃ ┗ 📜2025_05_18_074327_create_user_settings_table.php
 ┃ ┣ 📂Models
 ┃ ┃ ┗ 📜UserSetting.php
 ┃ ┣ 📂Providers
 ┃ ┃ ┗ 📜UserSettingServiceProvider.php
 ┃ ┗ 📂Services
 ┃ ┃ ┗ 📜UserSettingService.php
 ┗ 📂Users
 ┃ ┣ 📂Http
 ┃ ┃ ┣ 📂Controllers
 ┃ ┃ ┃ ┗ 📜LanguageController.php
 ┃ ┃ ┗ 📂Livewire
 ┃ ┃ ┃ ┗ 📜LanguageSelector.php
 ┃ ┗ 📂Providers
 ┃ ┃ ┗ 📜UserSettingServiceProvider.php

---

## 💡 Cómo contribuir

1. **Haz un fork** del repositorio
2. **Crea una rama** (`feature/nombre-funcionalidad`, `fix/bug-nombre`, etc.)
3. **Haz tus cambios** respetando la estructura de módulos y paquetes
4. **Agrega pruebas** si es necesario
5. **Haz commit** siguiendo buenas prácticas (`git commit -m "feat: nombre de la mejora"`)
6. **Abre un Pull Request** explicando claramente lo que hiciste

---

## 📦 Estructura esperada

- Usa `Modules/` para nuevas funcionalidades.
- Si tu funcionalidad es altamente reutilizable o externa, considera usar `packages/`.
- Usa componentes de Livewire si aplican.
- Sigue el diseño visual con Tailwind CSS y Blade.

---

## 🧪 Testing

Antes de enviar tu Pull Request, asegúrate de que todo pase:

```bash
php artisan test
```

## 🧼 Estilo de código
Sigue las convenciones de Laravel
Indentación con 4 espacios
Nombres descriptivos en clases, vistas y rutas
Comenta donde sea necesario

Gracias
# 🙌 Gracias
Toda contribución es bienvenida. ¡Gracias por hacer de MHartacho un mejor proyecto!