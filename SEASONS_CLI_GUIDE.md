# Guia d'Ús de Seasons via CLI

## Comandes Disponibles

### 1. Crear Temporades Acadèmiques

```bash
# Llistar configuracions disponibles
php artisan season:create --list

# Crear temporada acadèmica simple
php artisan season:create "Curs 2026-27"

# Crear temporada amb períodos automàtics
php artisan season:create "Curs 2026-27" --config=two_semesters

# Crear amb dates personalitzades
php artisan season:create "Curs 2026-27" --start="2026-09-01" --end="2027-06-30" --config=three_trimesters

# Crear i marcar com a actual i activa
php artisan season:create "Curs 2026-27" --config=two_semesters --current --active
```

#### Configuracions de Períodos Disponibles:

| Clau | Descripció | Períodos |
|------|------------|----------|
| `two_semesters` | 2 Semestres | 2 períodos de 6 mesos |
| `three_trimesters` | 3 Trimestres | 3 períodos de 4 mesos |
| `two_quarters` | 2 Quadrimestres | 2 períodos de 4 mesos |
| `trimester_plus_quarter` | 1 Trimestre + 1 Quadrimestre | 2 períodos: 3 mesos + 4 mesos |
| `four_bimensual` | 4 Bimensuals | 4 períodos de 2 mesos |
| `monthly` | 10 Mensuals | 10 períodos de 1 mes |

### 2. Gestionar Temporades Existents

```bash
# Llistar totes les temporades
php artisan season:manage list

# Activar una temporada
php artisan season:manage activate --id=1

# Desactivar una temporada
php artisan season:manage deactivate --id=1

# Establir com a temporada actual
php artisan season:manage current --id=1

# Esborrar una temporada (sense fills)
php artisan season:manage delete --id=1

# Forçar accions sense confirmació
php artisan season:manage activate --id=1 --force
```

### 3. Proves i Depuració

```bash
# Comand de proves (per desenvolupament)
php artisan season:test create --name="Test Season"
php artisan season:test generate
php artisan season:test list
```

## Exemples d'Ús

### Crear Any Acadèmic amb Semestres
```bash
php artisan season:create "Curs 2026-27" --config=two_semesters --current --active
```

### Crear Any amb Trimestres
```bash
php artisan season:create "Curs 2026-27" --config=three_trimesters --start="2026-09-01" --end="2027-06-30"
```

### Crear Any amb Períodes Mensuals
```bash
php artisan season:create "Curs 2026-27" --config=monthly
```

### Canviar Temporada Actual
```bash
# Primer veure les temporades
php artisan season:manage list

# Després establir la nova actual
php artisan season:manage current --id=5
```

## Avantatges de la Solució CLI

1. **Robustesa**: Funciona sempre, no depèn de JavaScript
2. **Control Total**: Accés complet a totes les funcionalitats
3. **Automatització**: Pot integrar-se amb scripts i cron jobs
4. **Seguretat**: Menys superfície d'atac que la UI
5. **Velocitat**: Més ràpid que les interfícies web

## Estructura de Dades

- **Temporades Acadèmiques** (`type: annual`): Són els pares
- **Períodes Fills** (`type: semester, trimester, etc.`): Tenen `parent_id`
- **Jerarquia**: Un any acadèmic pot tenir múltiples períodes fills
- **Dates de Registre**: Poden estar fora del període de temporada

## Solució de Problemes

### Error: "Column not found: created_by"
Aquest error està corregit. El servei `SeasonPeriodGenerator` ja no intenta usar aquest camp.

### Error: "Configuration not found"
Usa `php artisan season:create --list` per veure les configuracions vàlides.

### Error: "No academic year found"
Assegura't que la temporada que intentes modificar té `type: annual` i `parent_id: null`.

## Notes Importants

- Les dates per defecte són: 1 de setembre a 30 de juny
- Només es pot esborrar temporades sense períodes fills
- Només pot haver-hi una temporada `is_current = true` alhora
- Les dates de registre es generen automàticament si no s'especifiquen
