# 📋 Requisits per Executar el Seeder d'Importació de Matrícules

## 📁 Fitxers CSV Requerits

### 📚 Cursos CSV
```
📍 Ruta: storage/app/imports/cursos_upg.csv
📋 Format: CSV sense cometes
🔤 Estructura: code,title,hours
📝 Exemple: TxiJ(DIJ),Sent la vida amb el Txi Kung (dijous),40
```

### 💳 Ordres CSV
```
📍 Ruta: storage/app/imports/ordres_2025-26-Q2-v2.csv
📋 Format: CSV sense cometes
🔤 Estructura: firstname,lastname,email,phone,item_name,status,quantity,price
📝 Exemple: Rosa,Morillas,mrmorillasg7@gmail.com,605257022,Sent la vida amb el Txi Kung (dilluns),1,1,25.00
```

## 🔧 Variables d'Entorn

Afegir al fitxer `.env`:

```env
SEEDER_DEFAULT_PASSWORD=Campus2026!
SEEDER_EMAIL_DOMAIN=test.local
```

## ⚠️ Errors Comuns i Solucions

### 🚨 Error 1: Fitxers No Trobats
```
❌ Error: No es troben els fitxers CSV necessaris.
✅ Solució:
   - Verificar que existeixin a storage/app/imports/
   - Noms exactes: cursos_upg.csv, ordres_2025-26-Q2-v2.csv
```

### 🚨 Error 2: Format CSV Incorrecte
```
❌ Error: fgetcsv() no parseja correctament
✅ Solució:
   - El CSV ha d'anar SENSE cometes: camp1,camp2,camp3
   - NO amb cometes: "camp1","camp2","camp3"
```

### 🚨 Error 3: Posicions Incorrectes
```
❌ Error: Camps buits o incorrectes
✅ Solució:
   - Cursos: $data[0]=code, $data[1]=title
   - Ordres: $data[4]=item_name, $data[5]=status
```

### 🚨 Error 4: Concordança de Cursos Fallida
```
❌ Error: ⚠️ Ordre sense curs (99% dels casos)
✅ Solució:
   - Els títols han de coincidir exactament
   - Revisar espais, majúscules/minúscules
   - Ex: "Sent la vida amb el Txi Kung (dilluns)"
```

### 🚨 Error 5: Usuaris Duplicats
```
❌ Error: Violació de constraint unique a users.email
✅ Solució:
   - Netejar la taula users abans d'executar
   - php artisan tinker
   - User::truncate();
```

### 🚨 Error 6: Matrícules Duplicades
```
❌ Error: Violació de constraint unique a campus_registrations
✅ Solució:
   - Netejar la taula campus_registrations
   - CampusRegistration::truncate();
```

### 🚨 Error 7: Permisos de Fitxers
```
❌ Error: Permission denied storage/app/imports/
✅ Solució:
   - chmod -R 755 storage/
   - chown -R www-data:www-data storage/
```

## 🔍 Diagnòstic Ràpid

### 📊 Comandes de Verificació
```bash
# 1. Verificar fitxers
ls -la storage/app/imports/

# 2. Verificar format CSV
head -5 storage/app/imports/ordres_2025-26-Q2-v2.csv

# 3. Verificar base de dades
php artisan tinker
User::count();
CampusRegistration::count();
```

### 🎯 Comanda d'Execució Segura
```bash
# Executar només el seeder d'importació
php artisan db:seed --class=RegistrationImportSeeder

# O executar-ho tot (destructiu)
php artisan migrate:fresh --seed
```

## 📋 Checklist Abans d'Executar

### ✅ Preparació
- [ ] Fitxers CSV en ubicació correcta
- [ ] Format CSV sense cometes
- [ ] Variables d'entorn configurades
- [ ] Base de dades accessible
- [ ] Permisos d'escriptura a storage/

### ✅ Durant l'Execució
- [ ] Monitoritzar logs d'errors
- [ ] Verificar estadístiques finals
- [ ] Revisar fitxers de report generats

### ✅ Post-Execució
- [ ] Verificar usuaris creats
- [ ] Verificar matrícules creades
- [ ] Revisar reportes generats
- [ ] Validar dades a base de dades

## 🚀 Solució de Problemes Avançada

### 🔧 Debug Mode
```php
// En RegistrationImportSeeder.php
$this->command->info("DEBUG: " . print_r($orden, true));
```

### 📊 Validació de Dades
```php
// Validar estructura del CSV
if (count($data) < 8) {
    $this->command->error("Línia invàlida: " . implode(',', $data));
    continue;
}
```

## 📈 Estadístiques Esperades

Un cop executat correctament, hauríeu de veure:

```
📊 Ordres processades: 798
👤 Usuaris creats: X
👥 Usuaris existents: Y
🎓 Alumnes creats: X
🎓 Alumnes existents: Y
✅ Matrícules creades: Z
❌ Matrícules impossibles: W
⚠️  Ordres sense email: V
⚠️  Ordres sense curs: U
```

## 📝 Notes Importants

- **🔄 El seeder és idempotent**: Si s'executa múltiples vegades, no crearà duplicats
- **📊 Els reportes es guarden automàticament** a `storage/app/imports/`
- **🔍 Els missatges d'error inclouen emails** per facilitar la depuració
- **🎯 La concordança de cursos és sensible a majúscules/minúscules**
- **💡 Es recomana provar amb dades de mostra abans de produir**

## 🆘 Suport

Si trobeu errors no documentats:

1. **Revisar els logs** de Laravel a `storage/logs/laravel.log`
2. **Verificar el format CSV** amb un editor de text
3. **Provar amb dades reduïdes** per identificar el problema
4. **Consultar els reportes generats** per veure detalls dels errors

---

*Documentació actualitzada: 21/02/2026*
