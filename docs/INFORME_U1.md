# Evaluación Unidad 1

## Desarrollo de Sistema de Gestión de Proyectos Tech Solutions

**Asignatura:** Desarrollo de Software Web I

**Profesor:** Víctor Cofré Farias

**Estudiantes:** Camilo González Moraga y Jeremías Roa Jerez

## 1. Identificación de los componentes principales del Framework

El sistema se construyó con Laravel 13 y Blade, el motor de plantillas integrado en Laravel. La aplicación usa rutas para recibir solicitudes, un controlador para coordinarlas, un modelo para entregar los proyectos y vistas para presentar la información. También incluye un componente reutilizable para mostrar la UF.

La API define cinco operaciones en `routes/api.php`: GET y POST en `/api/proyectos` para listar y solicitar la creación; GET, PUT y DELETE en `/api/proyectos/{id}` para consultar, actualizar y solicitar la eliminación de un proyecto. Estas rutas se conectan con las acciones `index`, `store`, `show`, `update` y `destroy` de `ProyectoController`. Las rutas web usan el mismo controlador para mostrar el listado, los formularios, el detalle y la confirmación de eliminación.

El modelo `Proyecto` entrega registros estáticos con los campos `Id`, `Nombre`, `Fecha_Inicio`, `Estado`, `Responsable` y `Monto`. El ejemplo contiene dos proyectos y no utiliza una tabla de proyectos. Las cinco plantillas Blade se encuentran en `resources/views/proyectos/`: listado, creación, edición, detalle y confirmación de eliminación. `UfWidget` y su plantilla Blade reúnen la consulta y la presentación del indicador económico en el listado.

## 2. Descripción de la arquitectura del Framework en función al requerimiento

La aplicación ofrece dos vías: las rutas `/api` responden JSON y las rutas web muestran HTML o redirigen. Laravel asocia cada solicitud con una acción del controlador y devuelve el formato correspondiente. Ambas vías comparten el modelo de datos estáticos y las reglas de validación.

En la API, GET `/api/proyectos` devuelve la colección y GET `/api/proyectos/{id}` devuelve un registro. POST recibe los cinco campos editables; PUT valida esos campos para el identificador indicado; DELETE identifica el proyecto. Las tres escrituras responden con estado 200 y un JSON con `message` y `datos`, pero no modifican la colección. Un identificador inexistente produce 404 y los datos inválidos reciben 422 con errores JSON, incluso sin una cabecera `Accept`.

Para la interfaz, `ProyectoController` obtiene los datos de `Proyecto`, devuelve la vista correspondiente o redirige al listado después de enviar un formulario. Las rutas web atienden las pantallas y la API intercambia datos JSON mediante los mismos métodos del controlador.

El componente UF consulta un servicio externo por HTTPS con verificación de certificado y un límite de espera de dos segundos. Comprueba la estructura de la respuesta, que el valor sea numérico, finito y positivo, y que la fecha sea válida. Solo muestra el registro correspondiente al día de Chile; si la fecha es anterior o futura, o la consulta falla, informa que la UF no está disponible para hoy.

## 3. Explicación de los patrones de diseño utilizados (MVC)

La organización sigue el patrón Modelo-Vista-Controlador. Cada parte cumple una función reconocible en las solicitudes web y en las operaciones de la API.

El **Modelo**, representado por `Proyecto`, concentra los datos de ejemplo. Su método `getDatosEstaticos()` entrega la misma colección al controlador para construir el listado o localizar un registro por `Id`. Aunque la clase está dentro de la capa de modelos de Laravel, los proyectos no se consultan ni se guardan en una base de datos.

El **Controlador**, `ProyectoController`, conecta las rutas con los datos y la respuesta. Obtiene la colección, comprueba que los identificadores existan, valida los campos de escritura y decide si debe devolver JSON, renderizar una vista o redirigir. Las rutas de la API y las web utilizan las acciones correspondientes del mismo controlador.

Las **Vistas** Blade presentan los proyectos y los formularios. Reciben los datos desde el controlador y muestran los valores con expresiones escapadas. Los formularios usan las rutas con nombre, protección CSRF y los métodos HTTP correspondientes. Esta separación permite que el modelo mantenga el origen de los datos, que el controlador aplique las reglas y que cada vista se centre en la presentación.

`UfWidget` complementa MVC como componente de vista reutilizable. Encapsula la consulta del indicador y ofrece sus datos a una plantilla propia, evitando repetir esa integración dentro de las pantallas de proyectos.

## 4. Interacción de interfaces y componentes reutilizables

El listado presenta los dos proyectos en una tabla con identificador, nombre, fecha de inicio, estado, responsable y monto. Desde allí se puede abrir el detalle, editar un proyecto, iniciar su eliminación o acceder al formulario de creación. La pantalla de detalle muestra los campos del registro seleccionado. La eliminación primero presenta una confirmación asociada al identificador elegido; el envío comunica que la operación fue simulada, pues los datos se conservan sin cambios.

Los formularios de creación y edición presentan los cinco campos editables del proyecto. Cada campo cuenta con una etiqueta asociada y usa los valores enviados previamente si la validación encuentra errores. Laravel valida en el servidor los datos requeridos, la fecha y el monto no negativo; la vista informa qué campos deben corregirse. Los formularios incluyen el token CSRF y representan las solicitudes de actualización y eliminación con los métodos PUT y DELETE. Después de una solicitud válida, el listado muestra el mensaje que explica que los datos no se modificaron.

El componente UF aparece en el listado y consulta el indicador en un servicio externo. Utiliza HTTPS con verificación de certificado y un tiempo máximo de espera de dos segundos. La interfaz muestra el valor solo si el registro es válido y corresponde al día de Chile. Ante un dato antiguo o futuro, una respuesta inválida o una falla de conexión, presenta el mensaje “UF no disponible para hoy”. Así evita mostrar otro valor como si fuera el del día.

La solución integra navegación web, formularios, respuestas JSON y consumo de un servicio externo en una demostración académica. La API permite probar las cinco operaciones solicitadas, pero las de escritura no persisten cambios. Esa limitación se refleja en los mensajes de respuesta y en el comportamiento del listado, que continúa mostrando los registros iniciales.

La verificación automatizada confirmó la navegación web, las respuestas de la API y los casos válidos, inválidos y fallidos del servicio UF.

### Nota sobre el uso de inteligencia artificial

Durante el desarrollo usamos inteligencia artificial para revisar el código, organizar tareas y documentar el trabajo. Partimos de que vivimos en una era agéntica: muchas soluciones de programación ya son conocidas y pueden delegarse. Esto permite dedicar más atención a la capa superior del problema, como la estructura del sistema, su diseño y la composición de sus partes.

Para orientar esa colaboración consideramos tres pilares. **Compounding engineering** consiste en conservar aprendizajes útiles para tareas futuras. Las decisiones, instrucciones y hallazgos quedan en archivos del repositorio, junto al código. **Harness engineering** es preparar el entorno, las reglas y las comprobaciones que guían al agente dentro del alcance del proyecto. **Prompt engineering** consiste en expresar con claridad el objetivo, contexto y resultado esperado al delegar una tarea.

La IA puede ayudar a producir o revisar cambios, pero no reemplaza nuestra comprensión del sistema. Nosotros definimos el alcance, comprobamos el comportamiento de la aplicación y evaluamos las propuestas. La responsabilidad por el trabajo presentado y por el uso de estas herramientas sigue siendo nuestra.
