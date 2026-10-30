# Tech Solutions

Prototipo académico de gestión de proyectos con Laravel 13.39.0 y Blade. Los proyectos son estáticos: crear, editar y eliminar no guarda cambios.

Autores: Camilo González Moraga y Jeremías Roa Jerez.

## Requisitos

Para Windows, Linux o macOS:

- PHP 8.5.12 probado (mínimo 8.4.1), con las extensiones requeridas por Composer y SQLite (`pdo_sqlite` y `sqlite3`).
- Composer 2 y Git.
- Node.js 24.20.0 y npm 11.19.0 probados. La versión de Node está en `.nvmrc`; debe seleccionarse con un gestor compatible o instalarse desde [Node.js](https://nodejs.org/dist/v24.20.0/).
- Internet para instalar dependencias y descargar las fuentes del frontend.

## Instalación

Con los requisitos disponibles en la terminal:

```sh
git clone https://github.com/cgnzlez326/tech-solutions.git
cd tech-solutions
composer run setup
```

El setup instala dependencias, prepara `.env`, clave y SQLite, ejecuta migraciones y compila el frontend. Se puede repetir conservando configuración y datos; solo admite el entorno `local` y SQLite predeterminado. No requiere MySQL.

En Linux/macOS, PHP necesita permiso de escritura en `storage`, `bootstrap/cache` y `database`. El setup se probó en Windows; Linux/macOS aún no se ejecutaron.

## Desarrollo

Abrir dos terminales en la carpeta del proyecto.

**Backend:**

```sh
php artisan serve --host=127.0.0.1 --port=8000
```

**Frontend:**

```sh
npm run dev -- --host=127.0.0.1
```

Abrir [la aplicación](http://127.0.0.1:8000/proyectos). Detener cada proceso con `Ctrl+C`.

Vite sirve CSS y JavaScript. Las vistas de proyectos actuales tienen estilos integrados, por lo que funcionan con el backend y todavía no utilizan Vite.

## Con Laragon

1. Clonar en `C:\laragon\www\tech-solutions`.
2. Seleccionar PHP 8.5.12 **Thread Safe x64** para Apache y Node 24.20.0. Abrir una terminal nueva y comprobar `php -v`, `node -v` y `npm -v`.
3. Ejecutar `composer run setup` dentro del proyecto.
4. Configurar el sitio con **DocumentRoot en `tech-solutions/public`**, permitir `.htaccess`, habilitar `mod_rewrite` y recargar Apache. Ajustar `APP_URL` en `.env` al dominio local.

Si Node 24.20.0 no aparece, añadir esta entrada en **Tools → Quick add → Configuration**, instalarla desde Quick add y seleccionarla en **Node.js → Version**:

```ini
node-24.20=https://nodejs.org/dist/v24.20.0/node-v24.20.0-win-x64.zip
```

Apache ejecuta el backend; mantener solo la terminal de `npm run dev -- --host=127.0.0.1` para Vite. También está disponible [la URL de la carpeta local](http://localhost/tech-solutions/public/proyectos). Las reglas Apache incluidas permiten únicamente acceso local y protegen la raíz del proyecto.

## Pruebas y documentación

Ejecutar `php artisan test`. Sigue pendiente una prueba que espera 200 en `/`, aunque esa ruta redirige con 302.

- [Auditoría y problemas conocidos](docs/AUDITORIA.md).
- [Convenciones para agentes](AGENTS.md).
