# Tech Solutions — Gestión de Proyectos

Prototipo académico de Desarrollo de Software Web I, desarrollado por Camilo González Moraga y Jeremías Roa Jerez, profesor Víctor Cofré Farías.

Aplicación Laravel con vistas Blade, rutas web y un componente reutilizable para consultar la UF en mindicador.cl. El modelo `Proyecto` entrega dos registros estáticos: no utiliza una tabla de proyectos ni guarda los cambios enviados por los formularios.

## Requisitos

- Git y Composer 2.
- PHP **8.4.1 o superior**, con las extensiones exigidas por Composer y `pdo_sqlite`/`sqlite3`. Esta copia se comprobó con **PHP 8.5.12** y **Laravel 13.39.0**.
- Extensiones PHP: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `iconv`, `json`, `mbstring`, `openssl`, `pcre`, `PDO`, `pdo_sqlite`, `session`, `sqlite3`, `tokenizer`, `xml`, `xmlwriter`. `zip` facilita instalar dependencias.
- Acceso a Internet para descargar dependencias y consultar la UF.
- Node.js y npm son opcionales para las pantallas actuales: sus estilos están dentro de las vistas y no usan `@vite`.

`composer.json` permite PHP `^8.3`, pero **las versiones fijadas en `composer.lock` requieren al menos PHP 8.4.1**. No ejecutar `composer update` ni ignorar los requisitos de plataforma para evitar este requisito.

### Preparar el sistema

- **Windows:** instalar Git, Composer y PHP desde sus sitios oficiales, o usar Laragon. En Laragon, seleccionar PHP 8.5.12 y abrir una terminal nueva desde Laragon para actualizar el PATH. Si Apache carga PHP como módulo, usar PHP Thread Safe x64. Activar las extensiones anteriores en el `php.ini` de esa versión.
- **macOS:** con Homebrew instalado, `brew install php composer git`. Verificar que PHP cumpla la versión mínima y que SQLite esté habilitado.
- **Linux:** instalar Git, Composer 2 y una versión compatible de PHP mediante el gestor de paquetes de la distribución. Incluir los paquetes/extensiones de CLI, SQLite, mbstring, curl, XML y zip para esa misma versión. Si la distribución entrega PHP anterior a 8.4.1, instalar una versión compatible antes de continuar.

Consultar las instrucciones de [PHP](https://www.php.net/manual/en/install.php), [Composer](https://getcomposer.org/download/), [Git](https://git-scm.com/downloads) y [Laragon](https://laragon.org/docs/operations).

Comprobar la terminal que se utilizará:

```sh
php -v
php --ini
php -m
composer --version
git --version
```

El PHP de consola y el de Apache pueden ser distintos. Comprobar ambos si se utiliza un servidor web.

## Instalación desde un clon limpio

Los siguientes comandos funcionan en Bash (Linux/macOS), PowerShell y la terminal de Laragon. Ejecutarlos dentro de una carpeta nueva:

```sh
git clone https://github.com/cgnzlez326/tech-solutions.git
cd tech-solutions
composer install
composer check-platform-reqs
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```

Abrir <http://127.0.0.1:8000/proyectos>. Para detener el servidor, pulsar `Ctrl+C`.

La configuración de ejemplo utiliza SQLite. Las migraciones crean las tablas de sesiones, caché, usuarios y trabajos; **no crean proyectos**, porque estos son estáticos. No hace falta instalar MySQL. Cada clon genera su propio `.env`, clave y archivo SQLite: ninguno se descarga del repositorio.

En Linux/macOS, el usuario que ejecuta PHP necesita permiso de escritura en `storage`, `bootstrap/cache` y `database`. Si se utiliza Apache o PHP-FPM, concederlo mediante el propietario o grupo del proceso web; no usar permisos `777`.

No usar `composer run setup` en una instalación que ya tenga datos: genera una clave nueva y ejecuta pasos adicionales. Los comandos anteriores permiten revisar cada paso.

## Laragon y Apache

1. Clonar en la carpeta de proyectos de Laragon, por ejemplo `C:\laragon\www\tech-solutions`.
2. Seleccionar PHP 8.5.12, abrir una terminal nueva y ejecutar los pasos de instalación anteriores hasta `php artisan migrate`.
3. Para un dominio local, apuntar el **DocumentRoot del sitio a `tech-solutions/public`**, habilitar `mod_rewrite` y permitir las reglas de `.htaccess`. Recargar Apache después de cambiar su configuración.
4. Ajustar `APP_URL` en `.env` al dominio local utilizado. No publicar `.env`.

La copia de Laragon también se comprobó mediante <http://localhost/tech-solutions/public/proyectos>.

Se conservan dos protecciones deliberadas: `.htaccess` en la raíz deniega el acceso al código y `public/.htaccess` contiene `Require local`. Por eso Apache solo admite visitas desde el equipo local y acceder a `/tech-solutions/` devuelve 403; usar la URL con `/public/proyectos` o un sitio cuyo DocumentRoot sea `public`. Para exponer el proyecto en otro entorno, configurar primero los controles de acceso del servidor. No eliminar estas protecciones para resolver un 403 sin revisar la causa.

El servidor `php artisan serve` es para desarrollo y no procesa las reglas `.htaccess`; mantenerlo enlazado a `127.0.0.1`.

## Recursos de frontend opcionales

Solo es necesario compilar si se empieza a utilizar el CSS/JavaScript de Vite en las vistas:

```sh
npm install
npm run build
```

Instalar antes una versión de Node.js compatible con las dependencias de `package.json`. El repositorio conserva `.npmrc` con `ignore-scripts=true`; no deshabilitarlo de forma global para resolver un error de una dependencia. La instalación y compilación del frontend no son necesarias para abrir las pantallas actuales y no forman parte de la prueba de arranque descrita abajo.

## Comprobaciones y límites actuales

```sh
composer check-platform-reqs
php artisan route:list --except-vendor
php artisan view:cache
php artisan test
```

Comprobado en Windows con PHP 8.5.12 desde una copia limpia de los archivos versionados, sin reutilizar `vendor`, `.env` ni SQLite: `composer install`, requisitos de plataforma, generación de clave, creación de una base SQLite nueva, tres migraciones y compilación de Blade. El servidor de desarrollo respondió HTTP 200 en listado, creación, detalle y edición para un ID existente; `/` redirigió con 302. Las instrucciones de Linux/macOS usan los mismos comandos, pero no se ejecutaron en esos sistemas.

Límites conocidos:

- Crear, actualizar y eliminar solo redirigen al listado con un mensaje de éxito simulado. No validan ni guardan cambios. No utilizar el prototipo para datos reales.
- Las rutas devuelven HTML; no se ha implementado una API JSON de proyectos.
- Un ID inexistente puede producir HTTP 500 en vez de 404.
- La vista de eliminación no está enlazada y tiene fijado el ID `1`.
- La consulta UF depende de Internet, tiene un límite de dos segundos y puede mostrar indisponibilidad temporal. Actualmente desactiva la verificación del certificado TLS y no comprueba la fecha del valor recibido.
- La suite incluida tiene dos pruebas: una aprueba y otra falla porque espera HTTP 200 en `/`, aunque la ruta redirige con 302. Ese fallo pendiente no impide arrancar las pantallas.

Ver [la auditoría](docs/AUDITORIA.md) para los casos concretos y las correcciones propuestas.

## Estructura y trabajo con agentes

- `routes/web.php`: rutas web y nombres `proyectos.*`.
- `app/Http/Controllers/ProyectoController.php`: acciones de proyectos.
- `app/Models/Proyecto.php`: colección con datos estáticos.
- `resources/views/proyectos`: formularios, listado y detalle Blade.
- `app/View/Components/UfWidget.php` y `resources/views/components/uf-widget.blade.php`: consulta y presentación de la UF.
- `tests`: pruebas PHPUnit.

Antes de cambiar una funcionalidad, leer [AGENTS.md](AGENTS.md): describe las convenciones reales, qué revisar y qué verificar. [CLAUDE.md](CLAUDE.md) remite a la misma guía.

## Qué se publica

Se versionan fuentes, vistas, configuración sin secretos, migraciones, pruebas, documentación, `.env.example` y `composer.lock`. Se excluyen `.env` y sus variantes privadas, bases SQLite locales, dependencias descargadas, sesiones, registros, cachés, compilados, copias de respaldo y credenciales.

El repositorio es público. Revisar `git status` y `git diff --cached` antes de cada publicación; nunca añadir esos archivos con `git add -f`.
