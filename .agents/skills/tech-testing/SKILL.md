---
name: tech-testing
description: Elegir y ejecutar pruebas de PHP, Blade, setup y Vite en Tech Solutions. Usar al validar cambios de código, reproducir errores o comprobar instalación y compilación.
---

# Testing de Tech Solutions

Lee `AGENTS.md` y los requisitos actuales del `README.md`. Compara `php -v`, `node -v` y `npm -v` con los locks y `.nvmrc`; Apache y la consola pueden usar PHP distintos. Usa un binario explícito o cambia el PATH de la sesión si hace falta, sin modificar el entorno global.

Escoge la comprobación que ejercite lo cambiado:

| Cambio | Comprobación pertinente |
| --- | --- |
| Rutas/controladores/modelos | Prueba HTTP o de lógica en `tests/Feature` o `tests/Unit`; revisa `php artisan route:list --except-vendor` si cambian rutas. |
| Blade | `php artisan view:cache`, luego `php artisan view:clear`; revisa la pantalla afectada. |
| UF/red externa | `Http::fake(...)` con éxito y el fallo relevante: fecha antigua, estructura inválida o indisponibilidad. No hagas depender la suite de Internet. |
| Setup | `php artisan test --filter=SetupTest`; valida `composer run setup` en una copia limpia y repítelo conservando `.env`, clave y una fila centinela en SQLite. |
| Frontend o lock npm | `npm ci` y `npm run build`; comprueba el manifest. Si cambias desarrollo, inicia Vite en loopback, verifica los recursos y detén solo ese proceso. |
| Composer | `composer validate --no-check-publish` y `composer check-platform-reqs`; instala desde el lock. |

Para lógica no trivial deja una prueba pequeña que reproduzca el problema y compruebe el resultado; incluye el borde relevante. Una edición documental no necesita volver a compilar la aplicación.

Usa la configuración aislada de `phpunit.xml` para pruebas de la aplicación. No ejecutes setups ni migraciones de prueba sobre la base personal: usa una copia temporal sin `.env`, SQLite ni dependencias copiadas cuando estés verificando una instalación limpia. Conserva `.npmrc`; no desactives TLS ni controles de instalación para lograr un resultado verde.

Obtén una línea base cuando pueda haber fallos previos. La auditoría registra un caso 200/302: comprueba el comportamiento vigente y no cambies una redirección correcta solo para satisfacer una prueba obsoleta.

Cuando corresponda al alcance, termina con `php artisan test`. Tras pasar las comprobaciones pertinentes, amplía solo si queda un riesgo concreto. Informa comandos, resultados, fallos previos y sistemas operativos realmente probados; no conviertas estas ejecuciones en una bitácora del README.
