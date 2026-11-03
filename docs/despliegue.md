# Guía de despliegue en otro servidor

Procedimiento para llevar DentalDesk (código, migraciones y base de datos) a un
servidor nuevo. Está probado sobre Ubuntu 24.04, Apache 2.4, PHP 8.3 y
PostgreSQL 16, que es el entorno donde se desarrolló el proyecto.

## 1. Requisitos del servidor

| Componente | Versión mínima | Notas |
|---|---|---|
| PHP | 8.2 | Extensiones: `pdo_pgsql`, `pgsql`, `mbstring`, `xml`, `curl`, `zip`, `bcmath`, `intl`, `gd` |
| Composer | 2.x | Para instalar dependencias |
| PostgreSQL | 14 o superior | El proyecto **no** usa SQLite |
| Apache o Nginx | — | Con `mod_rewrite` si es Apache |
| Node.js | 18+ | Solo si vas a recompilar assets con Vite (opcional) |

Instalación rápida en Debian/Ubuntu:

```bash
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-pgsql php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd \
  postgresql postgresql-contrib apache2 libapache2-mod-php8.3 composer unzip git
```

## 2. Copiar el código

```bash
sudo mkdir -p /var/www/dentaldesk
sudo chown "$USER":www-data /var/www/dentaldesk
git clone <URL_DEL_REPOSITORIO> /var/www/dentaldesk
cd /var/www/dentaldesk
composer install --no-dev --optimize-autoloader
```

Si no usas git, copia el directorio completo excluyendo `vendor/` y vuelve a
ejecutar `composer install`.

## 3. Variables de entorno

El repositorio incluye un `.env` real (decisión del proyecto) y una plantilla
`.env.example`. **Revisa y ajusta como mínimo:**

| Variable | Qué poner |
|---|---|
| `APP_URL` | La URL pública del servidor nuevo (http/https) |
| `APP_ENV` | `production` en un servidor real |
| `APP_DEBUG` | `false` en producción |
| `APP_KEY` | Se conserva la del `.env` incluido, o se regenera con `php artisan key:generate` (esto invalida sesiones y datos cifrados) |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Datos de PostgreSQL del servidor nuevo |
| `TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_WHATSAPP_FROM` | Credenciales de Twilio |
| `SANCTUM_STATEFUL_DOMAINS` | Dominio público del servidor nuevo |

Si prefieres partir de la plantilla:

```bash
cp .env.example .env
php artisan key:generate
```

> **Seguridad:** el `.env` versionado contiene la `APP_KEY` y el
> `TWILIO_TOKEN` reales. Mantén el repositorio **privado** y, al terminar la
> migración, rota el token de Twilio y la contraseña de la base de datos.

## 4. Base de datos

### Opción A — Restaurar los datos actuales (recomendado para replicar)

```bash
sudo -u postgres psql <<'SQL'
CREATE USER dentaldesk_user WITH PASSWORD 'CAMBIAR_ESTA_CLAVE';
CREATE DATABASE dentaldesk_db OWNER dentaldesk_user;
SQL

pg_restore \
  --host=127.0.0.1 --port=5432 \
  --username=dentaldesk_user --dbname=dentaldesk_db \
  --no-owner --no-privileges --clean --if-exists \
  database/dumps/dentaldesk_db_2026-11-02.dump
```

Detalle y alternativa con SQL plano en `database/dumps/README.md`.

### Opción B — Base limpia con migraciones y seeders

```bash
php artisan migrate --seed
```

Esto crea el esquema y carga catálogos y usuarios de prueba. **No ejecutes
`db:seed` dos veces**: `DentistaSeeder` y `ConsultorioSeeder` insertan filas
nuevas en cada corrida.

### Comprobar

```bash
php artisan migrate:status
psql -h 127.0.0.1 -U dentaldesk_user -d dentaldesk_db -c '\dt'
```

## 5. Permisos de archivos

```bash
sudo chown -R "$USER":www-data /var/www/dentaldesk
sudo find /var/www/dentaldesk -type d -exec chmod 775 {} \;
sudo find /var/www/dentaldesk -type f -exec chmod 664 {} \;
sudo chmod -R ug+rwX storage bootstrap/cache
```

El usuario con el que corre Apache/PHP-FPM debe poder escribir en `storage/` y
`bootstrap/cache/`. Si Apache corre como otro usuario, ajusta el grupo.

## 6. Apache (VirtualHost)

```bash
sudo a2enmod rewrite
```

`/etc/apache2/sites-available/dentaldesk.conf`:

```apache
<VirtualHost *:80>
    ServerName dentaldesk.ejemplo.com
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

Con HTTPS, añade el certificado (por ejemplo con `certbot --apache`) y actualiza
`APP_URL` a `https://...`.

### Nginx (alternativa)

```nginx
server {
    listen 80;
    server_name dentaldesk.ejemplo.com;
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

## 7. Optimización para producción

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Cada vez que cambies el `.env` o el código, limpia y vuelve a cachear:

```bash
php artisan optimize:clear
php artisan optimize
```

## 8. Tareas programadas (recordatorios por WhatsApp)

El comando `dentaldesk:recordatorios` envía los recordatorios del día
siguiente y está agendado a las 20:00 en `routes/console.php`. El servidor
necesita el cron de Laravel:

```cron
* * * * * cd /var/www/dentaldesk && php artisan schedule:run >> /dev/null 2>&1
```

Edita con `crontab -e` con el usuario que ejecuta PHP.

Prueba manual:

```bash
php artisan dentaldesk:recordatorios
```

## 9. Webhook de Twilio

En la consola de Twilio, configura el número de WhatsApp con:

- **URL del webhook:** `https://TU_DOMINIO/webhook/whatsapp`
- **Método:** `POST`

El endpoint valida la cabecera `X-Twilio-Signature` con `TWILIO_TOKEN`. Si la
URL pública cambia (por ejemplo, al dejar ngrok), actualízala tanto en Twilio
como en `APP_URL`, porque la firma se calcula sobre la URL completa.

## 10. Verificación final

1. `php artisan test` → 5 pruebas deben pasar.
2. Abrir `/` sin sesión → redirige a `/login`.
3. Entrar con `admin@dentaldesk.test` / `password` (si restauraste el dump o
   corriste `UsuarioSeeder`).
4. Comprobar `/pacientes`, `/agenda` y el panel.
5. Enviar un `POST` sin firma al webhook → debe responder **403**.
6. Revisar `storage/logs/laravel.log` si algo falla.

### Usuarios de prueba

| Correo | Rol |
|---|---|
| `admin@dentaldesk.test` | administrador |
| `recepcion@dentaldesk.test` | recepcionista |
| `garcia@dentaldesk.test` | odontólogo (Dr. García) |
| `perez@dentaldesk.test` | odontólogo (Dr. Perez) |
| `mayta@dentaldesk.test` | odontólogo (Dra. Mayta) |

Contraseña para todos: `password`. **Cámbialas antes de usar el sistema con
pacientes reales.**

## 11. Problemas frecuentes

| Síntoma | Causa probable | Solución |
|---|---|---|
| Error 500 con `Permission denied` en `storage/framework/views` | Vistas compiladas de otro usuario | `php artisan view:clear` y revisar permisos |
| `could not find driver` | Falta `pdo_pgsql` | `sudo apt install php8.3-pgsql` y reiniciar PHP/Apache |
| `relation "sessions" does not exist` | Base sin migrar | `php artisan migrate` |
| El webhook responde 403 con peticiones legítimas | `APP_URL` no coincide con la URL configurada en Twilio | Igualar ambas y limpiar `config:cache` |
| Cambios en `.env` sin efecto | Configuración cacheada | `php artisan config:clear` |
| Los recordatorios no salen | Falta el cron de `schedule:run` | Configurar la entrada de cron |
