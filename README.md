# DentalDesk

Sistema de gestión de citas odontológicas: agenda por consultorio, fichas de
pacientes y recordatorios por WhatsApp con confirmación automática.

Construido con **Laravel 11**, **PostgreSQL** y **Bootstrap 5**. Incluye
autenticación, roles (administrador, recepcionista, odontólogo), alcance de
datos por doctor y validación de la firma de Twilio en el webhook.

---

## Contenido

- [Requisitos](#requisitos)
- [Instalación rápida](#instalación-rápida)
- [Base de datos](#base-de-datos)
  - [Opción A: restaurar los datos actuales](#opción-a-restaurar-los-datos-actuales)
  - [Opción B: base limpia con migraciones y seeders](#opción-b-base-limpia-con-migraciones-y-seeders)
- [Servidor web](#servidor-web)
- [Tareas programadas](#tareas-programadas)
- [Usuarios de acceso](#usuarios-de-acceso)
- [Verificación](#verificación)
- [Solución de problemas](#solución-de-problemas)
- [Documentación](#documentación)

---

## Requisitos

| Componente | Versión |
|---|---|
| PHP | 8.2 o superior (probado con 8.3) |
| Composer | 2.x |
| PostgreSQL | 14 o superior |
| Apache o Nginx | con reescritura de URLs |
| Node.js | 18+ (solo si recompilas assets con Vite) |

Extensiones PHP necesarias: `pdo_pgsql`, `pgsql`, `mbstring`, `xml`, `curl`,
`zip`, `bcmath`, `intl`, `gd`.

Instalación en Debian/Ubuntu:

```bash
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-pgsql php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd \
  postgresql postgresql-contrib apache2 libapache2-mod-php8.3 \
  composer unzip git
```

---

## Instalación rápida

```bash
# 1. Código
sudo mkdir -p /var/www/dentaldesk
sudo chown "$USER":www-data /var/www/dentaldesk
git clone https://github.com/marcelombs2109/dentaldesk.git /var/www/dentaldesk
cd /var/www/dentaldesk

# 2. Dependencias
composer install --no-dev --optimize-autoloader

# 3. Entorno (el repo ya incluye .env; si no, copia la plantilla)
cp -n .env.example .env
php artisan key:generate   # solo si el .env no trae APP_KEY

# 4. Base de datos (ver sección siguiente)
# 5. Permisos
sudo chown -R "$USER":www-data /var/www/dentaldesk
sudo chmod -R ug+rwX storage bootstrap/cache

# 6. Cachés de producción
php artisan optimize
```

Edita `.env` antes del paso 4 con los datos del servidor:

```dotenv
APP_URL=http://tu-dominio
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dentaldesk_db
DB_USERNAME=dentaldesk_user
DB_PASSWORD=tu-clave

TWILIO_SID=...
TWILIO_TOKEN=...
TWILIO_WHATSAPP_FROM="whatsapp:+14155238886"

SANCTUM_STATEFUL_DOMAINS=tu-dominio
```

> Si cambias el `.env` después de `php artisan optimize`, ejecuta
> `php artisan optimize:clear` y vuelve a cachear.

---

## Base de datos

### Opción A: restaurar los datos actuales

El repositorio incluye una copia completa (esquema + datos) en
`database/dumps/`. Es la vía para replicar el sistema tal como está.

```bash
sudo -u postgres psql <<'SQL'
CREATE USER dentaldesk_user WITH PASSWORD 'tu-clave';
CREATE DATABASE dentaldesk_db OWNER dentaldesk_user;
SQL

pg_restore \
  --host=127.0.0.1 --port=5432 \
  --username=dentaldesk_user --dbname=dentaldesk_db \
  --no-owner --no-privileges --clean --if-exists \
  database/dumps/dentaldesk_db_2026-11-02.dump
```

Alternativa con SQL plano:

```bash
gunzip -c database/dumps/dentaldesk_db_2026-11-02.sql.gz | \
  psql -h 127.0.0.1 -U dentaldesk_user -d dentaldesk_db
```

Comprobar:

```bash
php artisan migrate:status   # todas deben figurar como "Ran"
```

Detalle completo en [`database/dumps/README.md`](database/dumps/README.md).

> **No ejecutes `db:seed` si restauraste el dump**: los seeders de dentistas y
> consultorios insertan filas nuevas en cada corrida y duplicarían los datos.

### Opción B: base limpia con migraciones y seeders

Para una instalación desde cero, sin los datos de ejemplo:

```bash
sudo -u postgres psql <<'SQL'
CREATE USER dentaldesk_user WITH PASSWORD 'tu-clave';
CREATE DATABASE dentaldesk_db OWNER dentaldesk_user;
SQL

php artisan migrate --seed
```

Esto aplica las 9 migraciones y carga:

- `DentistaSeeder`: los doctores de ejemplo.
- `ConsultorioSeeder`: los dos consultorios.
- `UsuarioSeeder`: un usuario por rol (idempotente).

---

## Servidor web

### Apache

```bash
sudo a2enmod rewrite
```

`/etc/apache2/sites-available/dentaldesk.conf`:

```apache
<VirtualHost *:80>
    ServerName tu-dominio
    DocumentRoot /var/www/dentaldesk/public

    <Directory /var/www/dentaldesk/public>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/dentaldesk_error.log
    CustomLog ${APACHE_LOG_DIR}/dentaldesk_access.log combined
</VirtualHost>
```

```bash
sudo a2ensite dentaldesk
sudo systemctl reload apache2
```

### Nginx

```nginx
server {
    listen 80;
    server_name tu-dominio;
    root /var/www/dentaldesk/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

El `DocumentRoot` **debe** apuntar a `public/`, nunca a la raíz del proyecto.

---

## Tareas programadas

El comando `dentaldesk:recordatorios` envía los recordatorios del día siguiente
y está agendado a las 20:00 en `routes/console.php`.

```cron
* * * * * cd /var/www/dentaldesk && php artisan schedule:run >> /dev/null 2>&1
```

Pruébalo manualmente con:

```bash
php artisan dentaldesk:recordatorios
```

---

## Usuarios de acceso

Creados por `UsuarioSeeder`. Contraseña para todos: `password`.

| Correo | Rol | Vínculo |
|---|---|---|
| `admin@dentaldesk.test` | administrador | — |
| `recepcion@dentaldesk.test` | recepcionista | — |
| `garcia@dentaldesk.test` | odontólogo | Dr. García |
| `perez@dentaldesk.test` | odontólogo | Dr. Perez |
| `mayta@dentaldesk.test` | odontólogo | Dra. Mayta |

> Cámbialas antes de usar el sistema con pacientes reales.

---

## Verificación

```bash
php artisan test
```

Resultado esperado: **5 pruebas aprobadas**.

Comprobaciones manuales:

| Prueba | Resultado esperado |
|---|---|
| Abrir `/` sin sesión | Redirige a `/login` |
| Login con `admin@dentaldesk.test` / `password` | Entra al panel |
| `POST /webhook/whatsapp` sin firma | `403` |
| `php artisan migrate:status` | Todas en `Ran` |

Si algo falla:

```bash
tail -40 storage/logs/laravel.log
```

---

## Solución de problemas

| Síntoma | Causa | Solución |
|---|---|---|
| `dubious ownership in repository` | El repo pertenece a otro usuario | `sudo chown -R "$USER":www-data /var/www/dentaldesk` o `git config --global --add safe.directory /var/www/dentaldesk` |
| `Permission denied` en `storage/framework/views` | Vistas compiladas de otro usuario | `php artisan view:clear` y revisar permisos |
| `could not find driver` | Falta `pdo_pgsql` | `sudo apt install php8.3-pgsql` y reiniciar PHP/Apache |
| `relation "sessions" does not exist` | Base sin migrar | `php artisan migrate` |
| Error 500 tras cambiar `.env` | Configuración cacheada | `php artisan optimize:clear` |
| El webhook responde 403 con peticiones legítimas | `APP_URL` no coincide con la URL de Twilio | Igualar ambas y limpiar la caché de configuración |
| Los recordatorios no se envían | Falta el cron | Configurar `schedule:run` |

---

## Documentación

| Documento | Contenido |
|---|---|
| [`docs/despliegue.md`](docs/despliegue.md) | Guía extendida de despliegue en producción |
| [`database/dumps/README.md`](database/dumps/README.md) | Restauración y regeneración de la base |
| [`docs/sdd/`](docs/sdd/) | Especificaciones funcionales (autenticación, roles, alcance, webhook) |
| [`evidencia/README.md`](evidencia/README.md) | Capturas y comparación antes/después |

---

## Seguridad

Este repositorio incluye un `.env` de trabajo con credenciales reales para
facilitar la migración. Mantenlo **privado** y, tras instalar en el servidor
definitivo, rota como mínimo:

1. El **Auth Token de Twilio**.
2. La **contraseña de PostgreSQL**.
3. La **`APP_KEY`** (`php artisan key:generate`).
4. Cualquier **token de GitHub** usado para publicar.
