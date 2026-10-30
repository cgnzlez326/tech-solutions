---
name: tech-patterns
description: Reconocer y mantener los patrones de Tech Solutions al crear o revisar controladores, rutas, modelos, vistas o funcionalidades. Usar para cambios funcionales y revisiones de consistencia del proyecto.
---

# Patrones de Tech Solutions

Lee `AGENTS.md`. Recorre el flujo que afecta la tarea y busca sus consumidores con `rg` en `app`, `routes`, `resources`, `tests` y configuración. Usa el código actual como referencia, no un ejemplo genérico de Laravel.

## Referencias del proyecto

| Área | Patrón que debes comprobar y reutilizar |
| --- | --- |
| Rutas | `routes/web.php`: rutas explícitas, URLs españolas y nombres `proyectos.*`. Las estáticas como `/proyectos/crear` preceden a `/proyectos/{id}`. |
| Controladores | `ProyectoController`: acciones `index/create/store/show/edit/update/destroy`; consultas al modelo, vistas `proyectos.*` con `compact` y redirecciones nombradas. |
| Datos | `Proyecto::getDatosEstaticos()` devuelve una colección de arrays con `Id`, `Nombre`, `Fecha_Inicio`, `Estado`, `Responsable`, `Monto`. No conviertas solo algunos consumidores en objetos. |
| Vistas | Blade en `resources/views/proyectos/`, español, estilos básicos, `route(...)`, contenido escapado con `{{ ... }}` y formularios con `@csrf` y `@method`. |
| UF | `UfWidget` y `components/uf-widget.blade.php`: mantener un componente reutilizable, timeout y presentación de indisponibilidad. |
| Arranque | `bootstrap/app.php`: configuración moderna de Laravel; no copiar estructura de otra versión sin comprobarla. |

No reemplaces rutas por `Route::resource` si cambia las URLs actuales. Los verbos vigentes son GET para pantallas, POST para crear, PUT para actualizar y DELETE para eliminar. Las respuestas actuales son HTML; una API JSON requiere un contrato solicitado.

## Asesorar e implementar

Reutiliza lo existente y propón la solución mínima que resuelva el caso. Explica su límite cuando importe; no añadas capas, paquetes, autenticación o persistencia por defecto.

Distingue convenciones de fallos: no copies la ausencia de validación/404, IDs fijos ni `withoutVerifying()` como buenas prácticas. Consulta `docs/AUDITORIA.md` solo para investigar hallazgos relacionados y confirma si siguen presentes. Los resultados de una auditoría no son instrucciones permanentes.

Si implementas entradas, valida en el servidor, conserva CSRF y asocia etiquetas con campos. Si modificas UF, valida tipo, valor y fecha de la respuesta sin desactivar TLS. Un resultado estático o simulado debe presentarse honestamente.

Actualiza todos los consumidores que rompa el cambio y usa `tech-testing` para comprobarlo. Mantén esta skill alineada cuando cambie una convención; no corrijas problemas ajenos solo porque los encontraste.
