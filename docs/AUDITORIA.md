# Auditoría funcional de Tech Solutions

Fecha: 29 de octubre de 2026.

**Qué hace este proyecto:** presenta un gestor académico de proyectos con Laravel y Blade. Lee dos proyectos estáticos y muestra un indicador UF desde un servicio externo. Se evaluó como demostración local de uno o pocos usuarios, sin exigir una aplicación de producción ni persistencia de proyectos.

Esta revisión propone cambios; no implementa correcciones. Las referencias corresponden al código revisado en esta fecha.

## Debe corregirse

1. **Un ID inexistente produce un error 500** (`app/Http/Controllers/ProyectoController.php:23`, `resources/views/proyectos/show.blade.php:8`, `resources/views/proyectos/edit.blade.php:14`).
   - **Qué hace:** busca el proyecto por `Id` y entrega el resultado a la vista.
   - **Problema:** `firstWhere` devuelve `null` para `999`, pero las vistas acceden a sus campos como si existiera. Detalle y edición carecen del control de ausencia.
   - **Cambio mínimo:** comprobar el resultado y devolver `abort(404)` antes de construir cada vista. Mantener los arrays y los nombres actuales; no hace falta convertir el modelo a consultas Eloquent.
   - **Si se omite:** un enlace equivocado rompe la página; con depuración local activa también muestra detalles internos del error.

2. **La conexión UF desactiva la verificación del certificado** (`app/View/Components/UfWidget.php:18`).
   - **Qué hace:** solicita el indicador económico por HTTPS.
   - **Problema:** `withoutVerifying()` permite aceptar un servidor con certificado inválido. El transporte no verifica que la respuesta pertenezca al servicio esperado.
   - **Cambio mínimo:** quitar esa llamada y configurar el almacén de certificados de PHP si el equipo falla al verificar HTTPS. Conservar el timeout y el mensaje de indisponibilidad.
   - **Si se omite:** una respuesta alterada puede mostrarse como un valor económico legítimo.

3. **Un valor UF con tipo inesperado puede romper todo el listado** (`app/View/Components/UfWidget.php:22`, `resources/views/components/uf-widget.blade.php:5`).
   - **Qué hace:** copia `serie[0].valor` desde JSON y lo pasa a `number_format`.
   - **Problema:** una respuesta HTTP 200 cuyo `valor` sea un array supera el constructor y falla al renderizar la vista. Se reprodujo con `Http::fake`, sin depender de la red; una serie vacía sí termina en el mensaje alternativo.
   - **Cambio mínimo:** extraer el valor con una lectura segura, comprobar que sea numérico y positivo, y usar el estado de indisponibilidad en otro caso.
   - **Si se omite:** una respuesta anómala del proveedor convierte un widget secundario en un error de toda la pantalla.

4. **El widget llama “de hoy” a cualquier fecha recibida** (`app/View/Components/UfWidget.php:24`, `resources/views/components/uf-widget.blade.php:2`).
   - **Qué hace:** utiliza el primer elemento de la serie como UF del día.
   - **Problema:** nunca inspecciona `fecha`. Una respuesta simulada con fecha `2000-01-01` y valor `123` se presentó como disponible bajo el texto “de hoy”.
   - **Cambio mínimo:** validar la fecha del registro contra el día de Chile, usando `America/Santiago`; mostrar la fecha real y un estado explícito cuando no corresponda al día. No confundir “último registro” con “hoy”.
   - **Si se omite:** un dato antiguo puede interpretarse como vigente aunque la consulta HTTP haya sido exitosa.

5. **La vista de eliminación no forma parte del flujo** (`routes/web.php:10`, `resources/views/proyectos/index.blade.php:50`, `resources/views/proyectos/delete.blade.php:14`).
   - **Qué hace:** existe una pantalla de confirmación, pero el botón del listado envía directamente `DELETE`.
   - **Problema:** no hay ruta ni acción que devuelva `proyectos.delete`; además, su formulario tiene el ID `1` escrito directamente. Se verificaron referencias en rutas, controlador, pruebas y vistas.
   - **Cambio mínimo:** si se mantienen las cinco vistas pedidas por el trabajo, conectar una ruta GET de confirmación a una acción que obtenga el proyecto, controle el 404 y entregue su ID real a la vista. El listado debe enlazar a esa confirmación.
   - **Si se omite:** la quinta vista sigue sin demostrarse y, al conectarla sin corregir el ID, siempre apuntaría al proyecto 1.

## Conviene corregir

6. **El contrato de escritura debe expresar que se trata de una simulación** (`app/Http/Controllers/ProyectoController.php:19`, `app/Models/Proyecto.php:10`, `resources/views/proyectos/index.blade.php:18`).
   - **Qué hace:** `store`, `update` y `destroy` redirigen con mensajes que incluyen “simuladamente”. El modelo vuelve a construir los mismos dos proyectos en cada llamada.
   - **Problema:** los formularios dicen guardar/actualizar/eliminar, pero las acciones ignoran los datos y el listado ni siquiera muestra `session('success')`. Enviar una creación no agrega una fila; eliminar el proyecto 1 no lo quita.
   - **Cambio mínimo:** conservar los datos estáticos exigidos por el ejercicio, indicar en la interfaz que no hay persistencia, mostrar el resultado simulado y validar los datos enviados antes de afirmar éxito. Si se decide implementar CRUD persistente, tratarlo como un cambio de alcance y probarlo; no agregar una base de datos por defecto.
   - **Si se omite:** el usuario no distingue la demostración de una operación completada, y entradas vacías o montos negativos pueden recibir la misma redirección de éxito.

7. **La prueba HTTP básica contradice la ruta actual y faltan pruebas del comportamiento real** (`tests/Feature/ExampleTest.php:15`, `tests/Unit/ExampleTest.php:12`, `routes/web.php:6`).
   - **Qué hace:** la prueba de integración espera 200 al visitar `/`; la otra solo comprueba que `true` es verdadero.
   - **Problema:** `/` redirige correctamente al listado con 302, por lo que la suite da una aprobada y una fallida. No detecta IDs ausentes ni respuestas UF anómalas.
   - **Cambio mínimo:** afirmar la redirección hacia `proyectos.index`; agregar una prueba de proyecto existente/ausente y pruebas del widget con `Http::fake` para valor válido, dato antiguo, tipo inválido y fallo de red. Las pruebas del listado deben simular HTTP y usar sesiones/caché en memoria.
   - **Si se omite:** la suite seguirá roja por un resultado esperado y dejará pasar los fallos que sí afectan las pantallas.

8. **Los campos del formulario no tienen etiquetas asociadas** (`resources/views/proyectos/create.blade.php:18`, `resources/views/proyectos/edit.blade.php:19`).
   - **Qué hace:** muestra un `label` antes de cada campo.
   - **Problema:** las etiquetas carecen de `for` y los campos de `id`; un lector de pantalla no tiene una asociación programática y pulsar la etiqueta no enfoca el campo.
   - **Cambio mínimo:** asignar un `id` único a cada campo y el mismo valor al `for` de su etiqueta. Mantener los nombres de entrada existentes.
   - **Si se omite:** los formularios siguen siendo más difíciles de completar con ayudas de accesibilidad.

9. **Composer informa una dependencia con aviso de seguridad** (`composer.lock`, paquete `symfony/mime`).
   - **Qué hace:** Laravel incluye este componente para construir mensajes MIME, como los usados en correo.
   - **Problema:** `composer audit --locked` informa el aviso de gravedad media `CVE-2026-45070` / `PKSA-wtxr-p26d-nn42` para la versión fijada `v8.1.7`. Permite inyectar cabeceras si una aplicación construye nombres de parámetros MIME desde entradas no confiables; no se encontraron llamadas propias de correo en `app`, `routes` ni `tests`, y no se demostró explotación en el flujo actual.
   - **Cambio mínimo:** actualizar únicamente `symfony/mime` a un parche compatible que incluya la corrección, revisar el diff del lock y repetir `composer audit --locked` y la suite. El [aviso oficial de Symfony](https://symfony.com/cve-2026-45070) identifica `8.1.10` como corregida en esa rama.
   - **Si se omite:** el clon conserva una dependencia conocida como afectada, aunque este prototipo no exponga actualmente el flujo de correo que necesita el ataque.

## Decisiones de alcance, no fallos inequívocos

- **API frente a HTML:** las ocho rutas registradas son web; las acciones retornan vistas o redirecciones, no una API JSON. La pauta habla de API y también pide vistas con datos estáticos. Debe aclararse esa interpretación antes de agregar `routes/api.php`, nuevos controladores o contratos JSON; una API completa no se justifica por defecto.
- **Edición parcial:** el formulario permite cambiar nombre, estado y monto; no ofrece fecha ni responsable. Si “actualizar proyecto” incluye todos los campos editables, añadir los dos campos faltantes y sus reglas de validación en el mismo flujo. El enunciado no define una política de edición por campo.
- **Latencia UF:** el widget hace una solicitud síncrona por cada visita al listado y puede esperar hasta dos segundos. Para esta demostración el timeout ya limita el daño; introducir una caché corta solo si se observa carga o latencia suficiente para justificarla, preservando la fecha del indicador.
- **Apache local:** `.htaccess` niega acceso a la raíz y `public/.htaccess` permite solo conexiones locales. Es una protección deliberada para Laragon, no una configuración de despliegue público. El servidor web de un despliegue debe apuntar a `public` y definir sus propias reglas de acceso.

## Comprobaciones realizadas

- PHP 8.5.12: comprobación de sintaxis de 29 archivos propios, sin errores.
- `php artisan test`: dos pruebas, una aprobada y una fallida por la diferencia 200/302 descrita arriba. La configuración de pruebas usa SQLite en memoria y sesiones/caché de tipo `array`.
- `php artisan route:list --except-vendor`: ocho rutas propias; no aparece una ruta de confirmación de eliminación ni rutas API.
- `composer audit --locked --no-interaction --format=json`: un aviso de seguridad de gravedad media en `symfony/mime`, sin paquetes abandonados. Se contrastó con el aviso oficial; el lock no fue modificado.
- Pruebas aisladas del widget con `Http::fake`: una fecha antigua se acepta, un array como valor falla al renderizar y una serie vacía muestra indisponibilidad. No se guardaron fixtures ni se hicieron solicitudes externas en estas comprobaciones.
- Se leyeron modelo/controlador de proyectos, widget y su vista, las cinco pantallas del flujo, configuración de arranque, configuración de base de datos, pruebas, manifiestos Composer/npm, Vite y reglas Apache. Se buscaron referencias antes de identificar la vista de eliminación desconectada.
- Las pantallas activas usan CSS integrado. Solo la plantilla de bienvenida sin ruta usa `@vite`; el build frontend no es requisito para abrir el flujo actual.

**Veredicto:** el prototipo arranca; primero corregir IDs ausentes, verificación TLS y validación/fecha del indicador UF. Después conectar la eliminación y ajustar la suite, conservando el alcance académico de datos estáticos.

**No comprobado:** build npm, instalación real en Linux/macOS, comportamiento con carga, proveedor UF en vivo y despliegue público. No se modificaron lógica, dependencias ni datos de proyectos; estas propuestas todavía requieren implementación y pruebas.
