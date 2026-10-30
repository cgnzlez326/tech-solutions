# Tech Solutions — Gestión de Proyectos

Prototipo académico de Desarrollo de Software Web I, desarrollado por Camilo González Moraga y Jeremías Roa Jerez, profesor Víctor Cofré Farías.

Aplicación Laravel con vistas Blade, rutas web y un componente reutilizable para consultar la UF en mindicador.cl. El modelo `Proyecto` entrega dos registros estáticos: no utiliza una tabla de proyectos ni guarda los cambios enviados por los formularios.

## Requisitos

- Git y Composer 2.
- PHP **8.4.1 o superior**, con las extensiones exigidas por Composer y `pdo_sqlite`/`sqlite3`. Esta copia se comprobó con **PHP 8.5.12** y **Laravel 13.39.0**.
- Extensiones PHP: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `iconv`, `json`, `mbstring`, `openssl`, `pcre`, `PDO`, `pdo_sqlite`, `session`, `sqlite3`, `tokenizer`, `xml`, `xmlwriter`. `zip` facilita instalar dependencias.
- Acceso a Internet para descargar dependencias y consultar la UF.
- Node.js **24.20.0** y npm **11.19.0**, versiones comprobadas para instalar y compilar el frontend. Los instaladores y binarios para cada sistema están en [la distribución oficial de Node 24.20.0](https://nodejs.org/dist/v24.20.0/). `.nvmrc` declara esa versión para gestores compatibles; no cambia por sí solo el Node del PATH. `package-lock.json` fija las dependencias npm.

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
node --version
npm --version
```

El PHP de consola y el de Apache pueden ser distintos. Comprobar ambos si se utiliza un servidor web.

## Instalación desde un clon limpio

Los siguientes comandos funcionan en Bash (Linux/macOS), PowerShell y la terminal de Laragon. Ejecutarlos dentro de una carpeta nueva:

```sh
git clone https://github.com/cgnzlez326/tech-solutions.git
cd tech-solutions
composer run setup
```

El comando instala dependencias de Composer desde el lock, comprueba PHP/extensiones, crea `.env` si falta, genera una clave solo cuando está vacía, crea SQLite si falta, ejecuta migraciones pendientes y prepara el frontend con `npm ci` y `npm run build`. Necesita Internet, también para las fuentes utilizadas durante el build. Se detiene si falla un paso y devuelve un código de salida distinto de cero; se puede repetir después de corregir la causa.

La configuración de ejemplo utiliza SQLite. Las migraciones crean las tablas de sesiones, caché, usuarios y trabajos; **no crean proyectos**, porque estos son estáticos. No hace falta instalar MySQL. Cada clon genera su propio `.env`, clave y archivo SQLite: ninguno se descarga del repositorio.

En Linux/macOS, el usuario que ejecuta PHP necesita permiso de escritura en `storage`, `bootstrap/cache` y `database`. Si se utiliza Apache o PHP-FPM, concederlo mediante el propietario o grupo del proceso web; no usar permisos `777`.

Se puede repetir el setup: conserva un `.env` existente, su clave no vacía y los datos de SQLite. Las migraciones se ejecutan de forma incremental; no usa `migrate:fresh`, `refresh` ni `--force`. Por seguridad, la preparación automática solo admite `APP_ENV=local`, la base SQLite predeterminada `database/database.sqlite` y ninguna `DB_URL`. Si existe configuración cacheada, ejecutar `php artisan config:clear` antes. Para otra base de datos o entorno, revisar su configuración y ejecutar las migraciones manualmente; el setup no modifica esas conexiones.

Si falta la variable `APP_KEY` en un `.env` existente, agregar una línea `APP_KEY=` antes de repetir el comando. No se reemplaza el archivo por el ejemplo para corregir ese error. El setup prepara el proyecto y termina; no deja servidores ejecutándose.

## Desarrollo con dos terminales

Después del setup, abrir dos terminales en la raíz del proyecto:

**Terminal 1 — backend Laravel:**

```sh
php artisan serve --host=127.0.0.1 --port=8000
```

**Terminal 2 — frontend Vite:**

```sh
npm run dev -- --host=127.0.0.1
```

Abrir <http://127.0.0.1:8000/proyectos>. Vite suele escuchar en el puerto 5173 y sirve recursos de frontend; la aplicación se visita en la URL del backend. Detener cada proceso con `Ctrl+C`.

Las vistas de proyectos actuales usan CSS integrado y no incluyen `@vite`: se pueden abrir solo con el backend y no reciben recarga de estilos desde Vite. La segunda terminal permite trabajar con los recursos que sí utilizan Vite; el setup no cambia las vistas para conectarlas automáticamente.

## Laragon y Apache

1. Clonar en la carpeta de proyectos de Laragon, por ejemplo `C:\laragon\www\tech-solutions`.
2. Seleccionar PHP 8.5.12 **Thread Safe x64** para el módulo Apache y activar las extensiones de PHP indicadas arriba. Abrir una terminal nueva desde Laragon, comprobar `php -v`, `node -v` y `npm -v`, y ejecutar `composer run setup` dentro del clon. Deben coincidir con las versiones compatibles/comprobadas descritas arriba.
3. Para un dominio local, apuntar el **DocumentRoot del sitio a `tech-solutions/public`**, habilitar `mod_rewrite` y permitir las reglas de `.htaccess`. Recargar Apache después de cambiar su configuración.
4. Ajustar `APP_URL` en `.env` al dominio local utilizado. No publicar `.env`.

Si Laragon administra Node, se puede agregar `node-24.20=https://nodejs.org/dist/v24.20.0/node-v24.20.0-win-x64.zip` a su configuración mediante **Menu → Tools → Quick add → Configuration**, instalar la entrada desde Quick add y seleccionarla en **Menu → Node.js → Version**. Abrir otra terminal y comprobar `node -v` y `npm -v`: Laragon puede dar prioridad a otra versión aunque Node 24.20.0 esté instalado globalmente.

La copia de Laragon también se comprobó mediante <http://localhost/tech-solutions/public/proyectos>.

Con Apache iniciado, este ya es el backend: no hace falta ejecutar además `php artisan serve`. Para desarrollar el frontend, abrir una terminal en el proyecto y ejecutar `npm run dev -- --host=127.0.0.1`. Visitar el dominio de Apache o la URL local anterior, no el puerto de Vite. Si se utiliza `php artisan serve` en lugar de Apache, seguir el flujo de dos terminales anterior.

Se conservan dos protecciones deliberadas: `.htaccess` en la raíz deniega el acceso al código y `public/.htaccess` contiene `Require local`. Por eso Apache solo admite visitas desde el equipo local y acceder a `/tech-solutions/` devuelve 403; usar la URL con `/public/proyectos` o un sitio cuyo DocumentRoot sea `public`. Para exponer el proyecto en otro entorno, configurar primero los controles de acceso del servidor. No eliminar estas protecciones para resolver un 403 sin revisar la causa.

El servidor `php artisan serve` es para desarrollo y no procesa las reglas `.htaccess`; mantenerlo enlazado a `127.0.0.1`.

## Compilación del frontend

El setup ya instala y compila el frontend. Para repetir esos pasos por separado:

```sh
npm ci
npm run build
```

Se comprobaron Node.js 24.20.0, npm 11.19.0, Vite 8.3.5, `npm ci`, `npm run build` y el servidor de desarrollo con respuestas HTTP 200 para el cliente Vite y las entradas CSS/JavaScript. `npm audit` no informó vulnerabilidades en el lock revisado. El repositorio conserva `.npmrc` con `ignore-scripts=true`; no deshabilitarlo de forma global para resolver un error de una dependencia. Los archivos compilados y `node_modules` permanecen excluidos de Git.

## Comprobaciones y límites actuales

```sh
composer check-platform-reqs
php artisan route:list --except-vendor
php artisan view:cache
php artisan test
```

Comprobado `composer run setup` en Windows con PHP 8.5.12, Node 24.20.0 y npm 11.19.0, desde una copia limpia en una ruta con espacios: instaló 110 paquetes Composer y 56 paquetes npm, generó clave y SQLite nuevos, ejecutó tres migraciones y compiló el frontend. Una segunda ejecución completa conservó el hash de `.env` y una fila centinela en SQLite. También se comprobaron la compilación de Blade y respuestas HTTP 200 en listado, creación, detalle y edición para un ID existente; `/` redirigió con 302. Las instrucciones de Linux/macOS usan los mismos comandos, pero no se ejecutaron en esos sistemas.

Límites conocidos:

- Crear, actualizar y eliminar solo redirigen al listado con un mensaje de éxito simulado. No validan ni guardan cambios. No utilizar el prototipo para datos reales.
- Las rutas devuelven HTML; no se ha implementado una API JSON de proyectos.
- Un ID inexistente puede producir HTTP 500 en vez de 404.
- La vista de eliminación no está enlazada y tiene fijado el ID `1`.
- La consulta UF depende de Internet, tiene un límite de dos segundos y puede mostrar indisponibilidad temporal. Actualmente desactiva la verificación del certificado TLS y no comprueba la fecha del valor recibido.
- La suite tiene cuatro pruebas: tres aprueban y una falla porque espera HTTP 200 en `/`, aunque la ruta redirige con 302. Los dos casos de `tests/Unit/SetupTest.php` comprueban que el setup conserve la configuración y los datos al repetirse, y rechace entornos/conexiones no admitidos o una variable `APP_KEY` ausente. Ese fallo previo de la ruta no impide arrancar las pantallas.

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
