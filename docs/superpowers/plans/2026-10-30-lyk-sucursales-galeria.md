# LyK — Sucursales y galería de posts Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** El administrador puede dar de alta sucursales (con foto, dirección, mapa y datos de contacto) que se muestran en el sitio, y puede agregar varias fotos, videos subidos y videos de YouTube/Facebook a cada post.

**Architecture:** `Branch` reutiliza el trait `Orderable` y el `OrderableController` existentes. La galería de posts es un modelo `PostMedia` (`image` | `video` | `embed`) que pertenece a `Post`. Los enlaces de video se convierten a URL de inserción con `App\Support\VideoEmbed`. Las vistas públicas siguen el sistema de diseño existente (tokens navy/leaf/gold/sage/cream, componentes `.btn-*`).

**Tech Stack:** el de la rama `feat/landing-lyk` (Laravel 13, Blade, Tailwind 4, Pest).

**Spec:** `docs/superpowers/specs/2026-10-29-lyk-landing-brief.md` más la petición del usuario del 2026-10-30: "que en la página se pueda visualizar cuántas sucursales tiene la empresa, ubicación, foto de referencia de la sucursal, etc., todo editable por el administrador; y que en los posts también se puedan agregar varias fotos y videos cortos". Decisión del usuario: los videos se pueden subir como archivo **y** pegar como enlace.

## Global Constraints

- Las Global Constraints del plan `2026-10-29-lyk-landing.md` siguen vigentes (español, tokens, 5 MB por imagen, `ImageUploader::RULES`).
- Videos subidos: `mp4, mov, webm` (mimetypes `video/mp4, video/quicktime, video/webm`), máximo 50 MB (`max:51200`).
- Enlaces de video aceptados: YouTube (`youtube.com/watch?v=`, `youtu.be/`, `youtube.com/shorts/`) y Facebook (`facebook.com/.../videos/...`, `fb.watch/...`). Cualquier otro enlace → error de validación en español.
- Hasta 10 fotos y 3 videos por envío del formulario.
- Límites PHP documentados: `upload_max_filesize = 60M`, `post_max_size = 128M`.
- No se siembran sucursales de ejemplo: no se inventan direcciones.

## Review Focus

1. **Enlace de video que no es YouTube/Facebook** (p. ej. `https://evil.com/x` o `javascript:`): debe rechazarse; nunca debe terminar como `src` de un iframe.
2. **Borrar un post con galería**: deben borrarse sus archivos de fotos y videos del disco.
3. **Borrar un elemento de la galería de otro post** (id cambiado a mano en la URL): 404, no borrar.
4. **Sin sucursales activas**: la página, la sección de inicio y el menú no deben mostrarse vacíos ni con "0 sucursales".
5. **Video `.mov` de iPhone**: se acepta, y el reproductor ofrece el enlace de descarga si el navegador no lo reproduce.

---

### Task 11: Sucursales

**Files:**
- Create: migración + `app/Models/Branch.php` + `BranchFactory`, `app/Http/Controllers/Admin/BranchController.php`, `app/Http/Controllers/BranchController.php`, vistas `admin/branches/{form,row}.blade.php`, `pages/branches.blade.php`, `partials/branches.blade.php`, `components/branch-card.blade.php`
- Modify: `routes/admin.php`, `routes/web.php`, `layouts/admin.blade.php` (menú), `partials/header.blade.php`, `partials/footer.blade.php`, `home.blade.php` + `HomeController`, `pages/contact.blade.php`, `admin/dashboard.blade.php`, `SitemapController`
- Test: `tests/Feature/BranchTest.php`

**Interfaces:**
- Consumes: `Orderable`, `OrderableController`, `ImageUploader`, `storage_url()`.
- Produces: `branches(name, address, city?, phone?, whatsapp?, email?, schedule?, maps_url?, map_embed_url?, photo_path?, sort_order, is_active)`; rutas `admin.branches.*` (`/admin/sucursales`), `branches` (GET `/sucursales`); componente `<x-branch-card :branch="" />`.

- [ ] **Step 1: Tests que fallan** — CRUD admin con foto (alta, edición conservando foto, borrar borra archivo); validación (`name`, `address` requeridos; `maps_url`/`map_embed_url` url; `whatsapp` regex de ajustes); `/sucursales` muestra "Contamos con 2 sucursales", nombre, dirección, foto, botón "Cómo llegar" con `maps_url`; sin sucursales activas `/sucursales` muestra mensaje y home/menú no muestran "Sucursales"; inactiva no aparece; singular "Contamos con 1 sucursal".
- [ ] **Step 2: Verlos fallar** — `.superpowers/t tests/Feature/BranchTest.php`.
- [ ] **Step 3: Implementar.** `BranchController` admin extiende `OrderableController` (`name()` = `branches`, `labels()` = `['Sucursales', 'Sucursal']`, `imageField()` = `['photo' => 'photo_path']`). Home: sección "Nuestras sucursales" con el contador y las tarjetas (máx. 3 + "Ver todas"). Contacto: lista compacta de sucursales en la columna lateral. Menú "Sucursales" sólo si hay activas (`View::composer` sobre `partials.header`/`partials.footer`). Sitemap incluye `/sucursales`.
- [ ] **Step 4: Verlos pasar** + suite completa.
- [ ] **Step 5: Commit** — `feat: sucursales administrables con foto y mapa`

### Task 12: Galería de fotos y videos en posts (modelo y panel)

**Files:**
- Create: migración + `app/Models/PostMedia.php` + factory, `app/Support/VideoEmbed.php`, `app/Http/Controllers/Admin/PostMediaController.php`, vista `admin/posts/media.blade.php`
- Modify: `Post` (`media()` hasMany ordenado), `PostRequest` (reglas), `Admin/PostController` (guardar/borrar media), `admin/posts/form.blade.php`, `routes/admin.php`, `ImageUploader::TOO_LARGE`
- Test: `tests/Unit/VideoEmbedTest.php`, `tests/Feature/Admin/PostMediaTest.php`

**Interfaces:**
- Produces:
  - `post_media(post_id fk cascade, type enum('image','video','embed'), path?, url?, caption?, sort_order)`.
  - `Post::media(): HasMany` ordenado por `sort_order, id`.
  - `PostMedia::publicUrl(): string` (archivo → `storage_url`, embed → `url`).
  - `VideoEmbed::fromUrl(string $url): ?string` → `https://www.youtube-nocookie.com/embed/{id}` o `https://www.facebook.com/plugins/video.php?href={urlencoded}&show_text=false`; `null` si no es válido.
  - Campos del formulario: `gallery_images[]`, `gallery_videos[]`, `video_url`, `media[{id}][caption]`, `media[{id}][sort_order]`.
  - Ruta `admin.posts.media.destroy` (DELETE `/admin/posts/{post}/media/{media}`, scoped).
  - `ImageUploader::VIDEO_RULES = ['file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:51200']`.

- [ ] **Step 1: Tests que fallan** — `VideoEmbed` (watch, youtu.be, shorts, facebook, fb.watch, rechaza `https://evil.com/x`, `javascript:alert(1)`, `https://youtube.com.evil.com/watch?v=x`); subir 2 fotos + 1 video + 1 enlace crea 4 `PostMedia` y los archivos existen; enlace inválido → error `video_url` en español y nada guardado; más de 10 fotos → error; video de 60 MB → error; editar caption/orden; borrar media borra archivo; borrar media de otro post → 404; borrar post borra archivos de media.
- [ ] **Step 2: Verlos fallar.**
- [ ] **Step 3: Implementar.** Fotos a `posts/gallery`, videos a `posts/videos`. El formulario de post muestra la galería actual (miniatura/ícono, caption, orden, botón Quitar con `form` HTML aparte vía atributo `form=`) y los campos para agregar.
- [ ] **Step 4: Verlos pasar** + suite.
- [ ] **Step 5: Commit** — `feat: galería de fotos y videos en posts (panel)`

### Task 13: Galería pública, límites y documentación

**Files:**
- Create: `resources/views/posts/gallery.blade.php`, `resources/js/gallery.js`
- Modify: `posts/show.blade.php`, `resources/js/app.js`, `components/post-card.blade.php` (indicador de fotos/videos), `public/.user.ini`, `README.md`, `errors/413.blade.php`
- Test: `tests/Feature/PostGalleryTest.php`

- [ ] **Step 1: Tests que fallan** — el post muestra imágenes de la galería con `loading="lazy"`, `<video controls preload="metadata" playsinline>` con enlace de descarga de respaldo, `<iframe>` con el embed y `title`; un post sin galería no muestra la sección "Galería"; el post-card muestra "3 fotos · 1 video" cuando aplica; `.user.ini` contiene `upload_max_filesize = 60M`.
- [ ] **Step 2: Verlos fallar.**
- [ ] **Step 3: Implementar.** Grid responsivo; clic en foto abre `<dialog>` a pantalla completa con anterior/siguiente y cierre con Esc (gallery.js); videos 16:9.
- [ ] **Step 4: Verlos pasar**, `npm run build`, revisión visual con Playwright (escritorio y 390 px).
- [ ] **Step 5: Commit** — `feat: galería pública de fotos y videos en posts`
