# Reglas de Tech Solutions

- Lee el código vigente antes de cambiarlo. Haz el cambio mínimo que complete la tarea.
- Conserva los contratos de rutas, claves de datos y vistas; actualiza sus consumidores si la tarea exige cambiarlos.
- El alcance actual es Laravel/Blade con proyectos estáticos. No agregues persistencia, frameworks o dependencias sin necesidad de la tarea.
- Respeta `.editorconfig`, los locks de dependencias y las protecciones Apache. Publica desde `public/`.
- No publiques secretos, bases locales, dependencias descargadas ni archivos generados. Conserva configuración y datos personales al probar.
- Informa qué cambió, qué se comprobó y los límites pendientes. Distingue fallos previos de regresiones.

## Skills del repositorio

- [tech-patterns](.agents/skills/tech-patterns/SKILL.md): reconocer y respetar patrones al implementar o revisar una funcionalidad.
- [tech-testing](.agents/skills/tech-testing/SKILL.md): elegir y ejecutar comprobaciones según el cambio.
- [tech-git](.agents/skills/tech-git/SKILL.md): revisar archivos publicables, preparar commits y sincronizar el repositorio.

El [README](README.md) contiene requisitos y arranque. La [auditoría](docs/AUDITORIA.md) es un diagnóstico histórico: comprueba cada hallazgo antes de tratarlo como vigente.
