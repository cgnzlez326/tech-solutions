---
name: tech-git
description: Preparar commits, ramas y PR en Tech Solutions; usar al versionar, publicar o revisar archivos publicables.
---

# Git de Tech Solutions

Trabaja desde la raíz que contiene `artisan` y `composer.json`. Lee `AGENTS.md` y consulta `git status --short`, `git diff` y `git remote -v` antes de mutar Git; no asumas que otra copia o el remoto de esta sesión siguen siendo los correctos.

## Unidad de cambio

- Cada cambio lógico, sea `docs`, `chore`, `fix` o `feat`, va en su propia rama y PR dirigido a `main`; agrupa los archivos necesarios para esa unidad, no abras un PR por archivo.
- Usa títulos de PR y mensajes de commit en español con el formato `tipo: descripción en español`, usando esos cuatro tipos. Describe el resultado final.
- Antes de integrar, publica la rama y abre el PR. No hagas push directo a `main` ni fusiones el PR sin instrucción explícita del usuario.
- Describe cada PR con estas secciones: `Qué hicimos`, `Cómo lo hicimos` y `Qué estamos resolviendo`. Completa el checklist con verificaciones reales; marca solo lo ejecutado y deja sin marcar o elimina lo que no aplique. La plantilla del repositorio está en `.github/pull_request_template.md`.

## Preparar lo publicable

- Incluye fuentes, pruebas, documentación, skills, `.env.example`, `composer.lock`, `package-lock.json` y `.nvmrc` cuando cambien.
- Verifica las reglas reales de `.gitignore` y los archivos ya versionados: ignorar un archivo no lo retira del índice. Nunca añadas secretos con `git add -f`.
- Excluye `.env` y variantes privadas, credenciales, SQLite, dumps, sesiones, logs, cachés, `vendor/`, `node_modules/`, `public/build/`, `public/hot` y copias de respaldo.
- Examina archivos nuevos y el diff preparado buscando credenciales, claves privadas y URLs con tokens. Informa ubicación y tipo de hallazgo sin copiar su valor. Comprueba tamaños antes de publicar binarios o archivos generados.

## Commit y sincronización

Añade rutas explícitas del cambio revisado. Comprueba `git diff --cached`, `git diff --cached --stat` y `git diff --cached --check`; no incluyas cambios ajenos ni reformatees código existente solo para limpiar el commit inicial.

Antes del commit, consulta la skill `tech-testing` si hay código modificado. No marques checks de pruebas que no se ejecutaron. La autorización explícita vigente para publicar o abrir un PR no debe volver a solicitarse.

Crear repositorios o cambiar su visibilidad requiere autorización; esta skill no la concede. No sobrescribas un repositorio remoto existente ni reescribas su historia para resolver un conflicto rutinario.

Tras publicar, comprueba rama, coincidencia del commit local/remoto y estado del árbol. Si falta autenticación, conserva la preparación local y comunica el bloqueo sin exponer credenciales.
