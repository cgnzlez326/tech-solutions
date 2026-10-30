---
name: tech-git
description: Preparar commits, revisar archivos publicables y sincronizar Git/GitHub en Tech Solutions. Usar al versionar o publicar cambios, o al auditar qué debe entrar al repositorio.
---

# Git de Tech Solutions

Trabaja desde la raíz que contiene `artisan` y `composer.json`. Lee `AGENTS.md` y consulta `git status --short`, `git diff` y `git remote -v` antes de mutar Git; no asumas que otra copia o el remoto de esta sesión siguen siendo los correctos.

## Preparar lo publicable

- Incluye fuentes, pruebas, documentación, skills, `.env.example`, `composer.lock`, `package-lock.json` y `.nvmrc` cuando cambien.
- Verifica las reglas reales de `.gitignore` y los archivos ya versionados: ignorar un archivo no lo retira del índice. Nunca añadas secretos con `git add -f`.
- Excluye `.env` y variantes privadas, credenciales, SQLite, dumps, sesiones, logs, cachés, `vendor/`, `node_modules/`, `public/build/`, `public/hot` y copias de respaldo.
- Examina archivos nuevos y el diff preparado buscando credenciales, claves privadas y URLs con tokens. Informa ubicación y tipo de hallazgo sin copiar su valor. Comprueba tamaños antes de publicar binarios o archivos generados.

## Commit y sincronización

Añade rutas explícitas del cambio revisado. Comprueba `git diff --cached`, `git diff --cached --stat` y `git diff --cached --check`; no incluyas cambios ajenos ni reformatees código existente solo para limpiar el commit inicial.

Antes del commit, consulta la skill `tech-testing` si hay código modificado. Redacta el mensaje según el resultado final, no como una bitácora de intentos.

Crear repositorios, cambiar su visibilidad y hacer push requiere autorización de la petición o del contexto vigente; esta skill no la concede ni exige volver a pedirla si ya existe. No sobrescribas un repositorio remoto existente ni reescribas su historia para resolver un conflicto rutinario.

Tras publicar, comprueba rama, visibilidad solicitada, coincidencia del commit local/remoto y estado del árbol. Si falta autenticación, conserva la preparación local y comunica el bloqueo sin exponer credenciales.
