# Guía para agentes de Tech Solutions

Estas instrucciones se aplican a todo el proyecto. Lee el código vigente antes de cambiarlo; esta guía describe su estructura, no sustituye su inspección. Las instrucciones explícitas del usuario tienen prioridad.

## Contexto y alcance

- Es un prototipo académico de gestión de proyectos con Laravel, MVC y vistas Blade en español.
- `composer.lock` fija Laravel 13.39.0. `composer.json` permite `^13.17`; no es Laravel 11. Las dependencias bloqueadas exigen PHP >= 8.4.1; el entorno comprobado usa PHP 8.5.12.
- Conserva las dependencias bloqueadas mediante `composer install`. No ejecutes `composer update`, cambies de framework o instales Boost u otras herramientas para tareas que no lo necesitan.
- Trabaja en la carpeta del proyecto indicada por el usuario; no edites otras copias ni configuraciones globales para resolver un cambio local.
- Haz el cambio más pequeño que complete lo pedido. No agregues capas, repositorios, servicios, paquetes o configuraciones especulativas.
- Cuando asesores sobre una solución, propón primero la opción mínima que resuelve todo el caso, reutilizando lo existente; explica sus límites y cómo comprobarla. Distingue recomendaciones de trabajo autorizado y evita alternativas especulativas.

## Antes de implementar

1. Lee `README.md`, las instrucciones de la carpeta y los archivos que tocarás.
2. Recorre el flujo completo: ruta, controlador, modelo, vista o componente y pruebas.
3. Busca todas las referencias del método, nombre de ruta o campo con `rg`. Incluye `app/`, `routes/`, `resources/`, `tests/`, configuración y fixtures cuando existan; excluye dependencias y archivos generados.
4. Identifica qué contratos deben conservarse: URLs, nombres de rutas, variables de las vistas, claves de los datos, validaciones y respuestas HTTP.
5. Comprueba las versiones reales de PHP y Composer; la consola y Apache pueden utilizar versiones diferentes. Usa un binario explícito o el PATH de la sesión si no coinciden.

Ejemplo de búsqueda antes de cambiar proyectos:

```sh
rg -n "getDatosEstaticos|ProyectoController|proyectos\." app routes resources tests
```

## Mapa y convenciones existentes

| Responsabilidad | Referencia y convención |
| --- | --- |
| Rutas web | `routes/web.php`: rutas explícitas, URLs en español y nombres `proyectos.*`. |
| Arranque y middleware | `bootstrap/app.php`: configuración moderna de Laravel con `Application::configure`. |
| Preparación local | `composer run setup` y `scripts/setup.php`: dependencias bloqueadas, entorno local SQLite y frontend; preservar `.env`, clave existente y datos al repetir. |
| Controlador | `app/Http/Controllers/ProyectoController.php`: recibe solicitudes, consulta el modelo y devuelve vistas o redirecciones. |
| Datos de proyectos | `app/Models/Proyecto.php`: `getDatosEstaticos()` devuelve una colección de arrays. |
| Pantallas | `resources/views/proyectos/`: Blade con HTML en español y estilos básicos integrados. |
| UF | `app/View/Components/UfWidget.php` y `resources/views/components/uf-widget.blade.php`, utilizado como `<x-uf-widget />`. |
| Pruebas | `tests/Feature/` para comportamiento HTTP; `tests/Unit/` para lógica aislada; configuración en `phpunit.xml`. |

### Controladores y datos

- Mantén el namespace `App\Http\Controllers` y la herencia de `Controller`. Reutiliza `ProyectoController` para operaciones del mismo recurso.
- Conserva las acciones `index`, `create`, `store`, `show`, `edit`, `update` y `destroy`; no crees un controlador por acción sin una necesidad concreta.
- Las lecturas usan `Proyecto::getDatosEstaticos()` y `firstWhere('Id', $id)`. Las vistas reciben `$proyectos` o `$proyecto` mediante `view('proyectos.…', compact(...))`.
- Los registros son arrays con claves exactas: `Id`, `Nombre`, `Fecha_Inicio`, `Estado`, `Responsable`, `Monto`. Las vistas acceden con `$proyecto['Nombre']`; no conviertas selectivamente los registros en objetos ni renombres las claves sin actualizar todos los consumidores.
- Aunque `Proyecto` hereda de Eloquent, actualmente no guarda proyectos en una tabla. No sustituyas los datos estáticos por persistencia ni agregues migraciones de proyectos salvo que lo requiera la tarea.
- Usa `Request` para entradas HTTP y validación de Laravel en el servidor cuando implementes escrituras. Los atributos HTML `required` no sustituyen esa validación.
- Una búsqueda sin resultado debe producir una respuesta controlada, normalmente 404, antes de renderizar una vista que espera un array.

### Rutas y formularios

- Mantén las rutas nombradas y genera enlaces/formularios con `route(...)`; no escribas URLs dependientes de Laragon o del sistema operativo.
- Conserva `/` como redirección a `proyectos.index` mientras no se solicite otro comportamiento.
- Declara rutas estáticas como `/proyectos/crear` antes de `/proyectos/{id}`. Si agregas otra acción por ID, revisa el orden y las restricciones de parámetros.
- La convención actual usa GET para pantallas, POST para crear, PUT para actualizar y DELETE para eliminar. No cambies URLs o verbos existentes incidentalmente ni reemplaces todo por `Route::resource` si modifica `/crear` o `/editar`.
- Los formularios Blade usan `method="POST"`, `@csrf` y `@method('PUT')` o `@method('DELETE')` cuando corresponde. Conserva esta protección.
- Estas son rutas web con respuestas HTML. El renderizado JSON de errores configurado en `bootstrap/app.php` no crea una API. Si se solicita una API, implementa su contrato y pruebas sin romper los consumidores web existentes.

### Vistas y componentes

- Conserva los nombres de vistas `proyectos.index`, `create`, `show`, `edit`, `delete` y las variables que utiliza cada una mientras sigan siendo necesarias.
- Reutiliza Blade y los estilos cercanos. No introduzcas otro framework de interfaz o un rediseño global para una funcionalidad puntual.
- Escapa contenido con `{{ ... }}`. Vincula etiquetas con sus campos, conserva navegación por teclado y muestra errores de validación cuando implementes formularios funcionales.
- Reutiliza `UfWidget` para mostrar la UF. La conexión externa no pertenece a cada acción del controlador ni a código PHP embebido en Blade.
- Conserva un tiempo límite y una presentación de indisponibilidad para fallos externos. Al mejorar el componente, valida estructura, valor y fecha de la respuesta; no copies `withoutVerifying()` como patrón de conexión segura.
- En pruebas de integración externa usa `Http::fake(...)`; verifica éxito, respuesta incompleta y fallo sin depender de la red real.

## Limitaciones conocidas: no convertirlas en convenciones

- `store`, `update` y `destroy` solo redirigen con mensajes de operación simulada. No validan entradas ni cambian datos. No presentes estos métodos como un CRUD completo.
- `show` y `edit` no controlan IDs inexistentes; las vistas pueden provocar un error 500. No repitas esa omisión.
- `delete.blade.php` no está conectado desde las rutas actuales y tiene el ID `1` fijo. Si se habilita la pantalla, recibe el proyecto seleccionado y usa su ID.
- El formulario de edición expone solo parte de los campos del modelo. Decide su alcance según el requerimiento, sin eliminar silenciosamente los demás datos.
- Los mensajes `success` se generan en el controlador, pero el listado no los muestra.
- La UF desactiva la verificación TLS y toma el primer elemento de `serie` sin comprobar fecha o estructura. Un valor recibido no demuestra que sea la UF de hoy.
- `tests/Feature/ExampleTest.php` espera HTTP 200 en `/`, pero la ruta redirige con 302. `tests/Unit/ExampleTest.php` es una prueba de ejemplo, no cobertura del negocio.

Verifica que estas limitaciones sigan presentes antes de reportarlas o corregirlas. No las arregles incidentalmente cuando el usuario solo pide documentación o revisión.

## Validación y entrega

- Para lógica no trivial añade una prueba pequeña que falle con el caso original y pase con el cambio. Incluye datos inválidos, ID inexistente o fallo externo cuando formen parte de lo modificado.
- Si modificas el setup, ejecuta `tests/Unit/SetupTest.php` y comprueba el comando completo en una copia aislada. No pruebes instalación/reinstalación sobre la base de datos personal.
- Ejecuta las comprobaciones pertinentes con PHP compatible:

```sh
composer validate --no-check-publish
composer check-platform-reqs
php artisan route:list --except-vendor
php artisan test
```

- Si cambias Blade, compila vistas con `php artisan view:cache` y luego elimina esa caché con `php artisan view:clear` para continuar el desarrollo. Comprueba las pantallas afectadas.
- Si cambias recursos administrados por Vite, ejecuta `npm run build` con las dependencias instaladas. Las pantallas de proyectos actuales usan CSS integrado; no necesitan un build frontend para mostrarse.
- Si una prueba falla antes de tu cambio, informa la línea base y separa ese fallo de los que introdujiste. No alteres respuestas correctas solo para satisfacer una prueba obsoleta.
- Sigue `.editorconfig`: UTF-8, LF, cuatro espacios y salto final. Conserva el estilo cercano; evita reformatear archivos ajenos al cambio.
- No publiques `.env`, credenciales, bases de datos locales, logs, sesiones, cachés, `vendor/` o `node_modules/`. Versiona ejemplos sin secretos y archivos de bloqueo de dependencias cuando existan.
- En Apache publica `public/`, nunca la raíz del proyecto. Conserva las restricciones locales de `.htaccess` salvo cambio de despliegue explícito; no desactives protecciones para conseguir una respuesta 200.
- Actualiza este documento si cambian contratos o estructura. Al terminar, informa qué cambió, cómo se comprobó, qué no se comprobó y los riesgos restantes.
