# Copias de la base de datos

Estos archivos contienen **el esquema y los datos reales** de DentalDesk al
2 de noviembre de 2026. Sirven para replicar el estado actual en otro servidor
sin volver a cargar los datos a mano.

| Archivo | Formato | Uso |
|---|---|---|
| `dentaldesk_db_2026-11-02.dump` | `pg_dump -Fc` (custom) | Recomendado. Permite restaurar con `pg_restore`, elegir tablas y es más pequeño. |
| `dentaldesk_db_2026-11-02.sql.gz` | SQL plano comprimido | Alternativa legible con `zcat` + `psql`, útil si no hay `pg_restore`. |

## Contenido

El dump incluye todas las tablas de la aplicación:

- `users`, `dentistas`, `consultorios`, `pacientes`, `citas`, `recordatorios`
- `migrations` (para que `migrate` sepa qué ya está aplicado)
- `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`,
  `password_reset_tokens`

Datos al momento de la copia:

| Tabla | Filas |
|---|---|
| `users` | 5 |
| `dentistas` | 3 |
| `consultorios` | 2 |
| `pacientes` | 9 |
| `citas` | 16 |
| `recordatorios` | 7 |

> **Privacidad:** el dump contiene datos personales (CI, teléfonos y
> observaciones de pacientes). No lo publiques en un repositorio abierto.

## Restauración

### 1. Crear la base y el usuario

Con un usuario de PostgreSQL que pueda crear roles y bases (normalmente
`postgres`):

```bash
sudo -u postgres psql <<'SQL'
CREATE USER dentaldesk_user WITH PASSWORD 'CAMBIAR_ESTA_CLAVE';
CREATE DATABASE dentaldesk_db OWNER dentaldesk_user;
SQL
```

### 2. Restaurar el dump custom (recomendado)

```bash
pg_restore \
  --host=127.0.0.1 \
  --port=5432 \
  --username=dentaldesk_user \
  --dbname=dentaldesk_db \
  --no-owner \
  --no-privileges \
  --clean --if-exists \
  database/dumps/dentaldesk_db_2026-11-02.dump
```

`--no-owner` y `--no-privileges` evitan errores si en el servidor nuevo el
usuario de base de datos se llama distinto. `--clean --if-exists` permite
repetir la restauración sobre una base que ya tenga tablas.

### 3. Alternativa con SQL plano

```bash
gunzip -c database/dumps/dentaldesk_db_2026-11-02.sql.gz | \
  psql --host=127.0.0.1 --username=dentaldesk_user --dbname=dentaldesk_db
```

### 4. Verificar

```bash
psql --host=127.0.0.1 --username=dentaldesk_user --dbname=dentaldesk_db -c "
  SELECT 'users' AS tabla, count(*) FROM users
  UNION ALL SELECT 'pacientes', count(*) FROM pacientes
  UNION ALL SELECT 'citas', count(*) FROM citas
  UNION ALL SELECT 'recordatorios', count(*) FROM recordatorios;"
```

Después, en la raíz del proyecto:

```bash
php artisan migrate:status   # todas las migraciones deben figurar como "Ran"
```

## Regenerar la copia

Desde el servidor con la base en uso:

```bash
export PGPASSWORD='...'
pg_dump -h 127.0.0.1 -U dentaldesk_user -d dentaldesk_db -Fc \
  -f database/dumps/dentaldesk_db_$(date +%F).dump
```

Si el `.env` apunta a otra base, ajusta `-d`, `-U` y `PGPASSWORD` según
`DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

## Instalación limpia sin datos

Si prefieres una base vacía (por ejemplo, para producción real), no restaures
el dump: crea la base y ejecuta

```bash
php artisan migrate --seed
```

Eso aplica las migraciones y carga los catálogos (`DentistaSeeder`,
`ConsultorioSeeder`) y los usuarios de trabajo (`UsuarioSeeder`). Revisa
`docs/despliegue.md` para el procedimiento completo.
