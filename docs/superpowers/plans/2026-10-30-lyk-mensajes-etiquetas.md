# LyK — Estados y etiquetas de mensajes Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** En la bandeja de mensajes del panel, el administrador puede agrupar/filtrar los mensajes por leído/no leído, por estado (pendiente, en proceso, atendido) y por etiquetas, y puede crear, editar y borrar sus propias etiquetas.

**Architecture:** `contact_messages` gana la columna `status`; `read_at` queda sólo para leído/no leído. Las etiquetas son el modelo `MessageTag(name, color)` con tabla pivote `contact_message_message_tag`. La bandeja filtra por query string (`?vista=`, `?etiqueta=`) y muestra contadores. Las etiquetas se administran en `/admin/etiquetas`.

**Tech Stack:** el de la rama `feat/landing-lyk`.

**Spec:** petición del usuario del 2026-10-30: "que los mensajes el admin lo pueda agrupar por etiquetas, leídos, no leídos, en proceso, pendientes, y que además el admin pueda crear nuevas etiquetas para los mensajes".

## Global Constraints

- Estados: `pending` → "Pendiente" (por defecto), `in_progress` → "En proceso", `done` → "Atendido".
- Vistas de la bandeja (`?vista=`): `todos`, `no-leidos`, `leidos`, `pendientes`, `en-proceso`, `atendidos`, cada una con su contador.
- Colores de etiqueta (lista cerrada, contraste AA con su texto): `gray, green, blue, gold, red, purple`.
- Nombre de etiqueta único, máximo 40 caracteres.
- Abrir un mensaje lo marca como leído (comportamiento actual); "Marcar como no leído" lo revierte.

## Review Focus

1. **Etiqueta borrada**: los mensajes que la tenían siguen existiendo y la pierden; un filtro `?etiqueta=` con id inexistente muestra la bandeja vacía, no un error.
2. **Filtro inválido** (`?vista=xyz`): se trata como `todos`.
3. **Asignar etiquetas con ids inexistentes** (formulario manipulado): error de validación, nada se guarda.
4. **Mensajes existentes** al migrar: quedan `pending`; los que ya estaban leídos siguen leídos.
5. **Nombre de etiqueta duplicado** (incluso con distinta mayúscula/minúscula): error en español.

---

### Task 14: Estados, etiquetas y bandeja agrupable

**Files:**
- Create: migraciones (`status` en `contact_messages`; `message_tags`; pivote), `app/Models/MessageTag.php` + factory, `app/Http/Controllers/Admin/MessageTagController.php`, vistas `admin/tags/{index,form}.blade.php`, `components/admin/tag-badge.blade.php`
- Modify: `ContactMessage` (constantes `STATUSES`, relación `tags()`, scopes), `Admin/MessageController` (filtros, `update`, `toggleRead`), `routes/admin.php`, `admin/messages/{index,show}.blade.php`, `layouts/admin.blade.php` (menú "Etiquetas"), `tests/Feature/Admin/MessageTest.php`
- Test: `tests/Feature/Admin/MessageTagTest.php`, `tests/Feature/Admin/MessageTest.php`

**Interfaces:**
- `ContactMessage::STATUSES = ['pending' => 'Pendiente', 'in_progress' => 'En proceso', 'done' => 'Atendido']`; `ContactMessage::VIEWS` (slug → closure de filtro); `tags(): BelongsToMany`.
- `MessageTag::COLORS = ['gray', 'green', 'blue', 'gold', 'red', 'purple']`; `messages(): BelongsToMany`.
- Rutas: `admin.tags.*` (`/admin/etiquetas`, resource sin show), `admin.messages.update` (PATCH `/admin/mensajes/{message}`: `status`, `tags[]`), `admin.messages.read` (PATCH `/admin/mensajes/{message}/leido`: alterna leído/no leído). La ruta `admin.messages.toggle` (`/atendido`) se elimina.

- [ ] **Step 1: Tests que fallan** — filtros y contadores de cada vista; filtro por etiqueta; vista inválida = todos; etiqueta inexistente = vacío; cambiar estado y etiquetas desde el mensaje; ids de etiqueta inexistentes → error; marcar no leído; CRUD de etiquetas con color válido; nombre duplicado (case-insensitive) → error; borrar etiqueta la quita de los mensajes; nuevo mensaje del formulario público nace `pending`.
- [ ] **Step 2: Verlos fallar.**
- [ ] **Step 3: Implementar.** Bandeja: pestañas con contadores arriba, fila de etiquetas (chips) para filtrar, cada mensaje muestra estado y etiquetas. Detalle: selector de estado, casillas de etiquetas, botón "Guardar", enlace "Crear etiqueta" y "Marcar como no leído".
- [ ] **Step 4: Verlos pasar**, suite completa, `npm run build`, revisión visual.
- [ ] **Step 5: Commit** — `feat: estados y etiquetas para organizar los mensajes`
