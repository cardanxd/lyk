# LyK Landing Page + Panel Admin — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Sitio público de LyK (home, posts, nosotros, contacto, aviso de privacidad) con un panel `/admin` protegido por login para editar todo el contenido, logo y portadas.

**Architecture:** Laravel 12 monolito con vistas Blade + Tailwind (Vite). Contenido de una sola instancia (misión, visión, contacto, logo…) en una tabla clave-valor `settings` cacheada; contenido en lista (portadas, posts, servicios, FAQ, testimonios, mensajes) en modelos Eloquent propios. Imágenes en el disco `public` con imágenes de marca de respaldo en `public/images/brand`.

**Tech Stack:** PHP 8.4, Laravel 12, MySQL 8 (SQLite en memoria para tests), Pest 3, Tailwind CSS 4 + Vite, Trix (editor de posts), `mews/purifier` (sanitizar HTML), `laravel-lang/common` (traducciones `es`).

**Spec:** `docs/superpowers/specs/2026-10-29-lyk-landing-brief.md`

## Global Constraints

- `APP_LOCALE=es`, `APP_TIMEZONE=America/Mexico_City`; toda la UI (pública y admin) y los mensajes de validación en español.
- Colores Tailwind: `navy #1B2A4A`, `leaf #5B9A3C`, `gold #E3A92B`, `sage #B9CDB0`, `cream #F7F5EF`.
- Fuentes: títulos **Libre Baskerville**, texto **Source Sans 3** (Google Fonts). Texto base 18px; botones ≥ 48px de alto.
- Imágenes subidas: `jpg, jpeg, png, webp`, máximo 5 MB (`max:5120`).
- Sin registro público de usuarios; el admin se crea por comando.
- Responsivo desde 360px de ancho.
- Lema por defecto: "Hacemos de tu retiro una nueva oportunidad".
- Posts: 9 por página.

## Review Focus

1. **Foto de celular demasiado grande o HEIC** (p. ej. 12 MB `.heic`): debe volver al formulario con un error en español, nunca un 500 ni un archivo guardado.
2. **Sin logo / sin portadas activas / sin posts**: el sitio debe mostrar el logo y la portada de marca incluidos y secciones vacías ocultas, no imágenes rotas ni errores.
3. **Falla del correo al recibir un contacto** (SMTP mal configurado): el mensaje debe quedar guardado en la bandeja y el visitante ver la confirmación.
4. **HTML peligroso en un post** (`<script>`, `onerror=`): debe eliminarse antes de guardar/mostrar.
5. **Intentos repetidos de login**: al 6.º intento en un minuto se bloquea con mensaje "Demasiados intentos".

---

### Task 1: Proyecto base, marca y layout público

**Files:**
- Create: proyecto Laravel en `/var/www/html/lyk` (junto a `recursos/` y `docs/`)
- Create: `public/images/brand/logo.png` (copia de `recursos/logo-removebg-preview.png`), `public/images/brand/portada.jpg` (copia de `recursos/493311514_…jpg`)
- Create: `resources/css/app.css` (tema Tailwind con los tokens), `resources/views/layouts/public.blade.php`, `resources/views/partials/header.blade.php`, `resources/views/partials/footer.blade.php`, `resources/views/home.blade.php` (provisional)
- Modify: `routes/web.php`, `.env.example`, `phpunit.xml`
- Test: `tests/Feature/HomeTest.php`

**Interfaces:**
- Produces: layout `layouts.public` con `@yield('title')`, `@yield('content')`, `@stack('meta')`; ruta `home` (`GET /`).

- [ ] **Step 1: Crear el proyecto y dependencias**

```bash
cd /var/www/html/lyk
composer create-project laravel/laravel tmp-app && cp -a tmp-app/. . && rm -rf tmp-app
composer require mews/purifier
composer require --dev pestphp/pest pestphp/pest-plugin-laravel laravel-lang/common --with-all-dependencies
./vendor/bin/pest --init
php artisan lang:add es
npm install && npm install trix
git init
```
Configura `.env.example`/`.env`: `APP_NAME="LyK Asesoría"`, `APP_LOCALE=es`, `APP_FALLBACK_LOCALE=es`, `APP_TIMEZONE=America/Mexico_City`, `DB_CONNECTION=mysql`, `DB_DATABASE=lyk`. En `phpunit.xml`: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`.

- [ ] **Step 2: Escribir el test que falla**

```php
it('muestra la home con el logo y el lema', function () {
    $this->get('/')->assertOk()
        ->assertSee('images/brand/logo.png')
        ->assertSee('Hacemos de tu retiro una nueva oportunidad');
});
```

- [ ] **Step 3: Correr y verlo fallar** — `./vendor/bin/pest tests/Feature/HomeTest.php` → FAIL (la vista de bienvenida no contiene el logo).

- [ ] **Step 4: Implementar layout, tema y ruta `home`**

`app.css` declara los colores y fuentes de Global Constraints en `@theme`. Header: logo + menú (Inicio, Posts, Nosotros, Contacto), hamburguesa en móvil con `<details>` (sin JS). Footer: contacto y enlace al aviso. La home provisional muestra la portada de marca y el lema fijo (Task 2 lo vuelve editable).

- [ ] **Step 5: Correr y verlo pasar** — mismo comando → PASS.

- [ ] **Step 6: Commit**

```bash
git add -A && git commit -m "feat: proyecto Laravel con marca LyK y layout público"
```

---

### Task 2: Ajustes clave-valor (`settings`)

**Files:**
- Create: `database/migrations/xxxx_create_settings_table.php`, `app/Models/Setting.php`, `app/Support/Settings.php`, `app/helpers.php` (autoload en `composer.json` → `files`), `database/seeders/SettingsSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`, vistas de Task 1 (usar `setting()`)
- Test: `tests/Feature/SettingsTest.php`

**Interfaces:**
- Produces:
  - Tabla `settings(key string primary, value text nullable, timestamps)`.
  - `App\Support\Settings` (singleton): `get(string $key, ?string $default = null): ?string`, `set(string $key, ?string $value): void`, `setMany(array $values): void`, `imageUrl(string $key, string $fallbackAsset): string` (URL de `Storage::disk('public')` si el valor existe y el archivo existe; si no `asset($fallbackAsset)`). Cache `settings.all` sin expiración, se borra en `set`/`setMany`.
  - Helper global `setting(string $key, ?string $default = null): ?string`.
  - Constante `Settings::KEYS` (lista de claves válidas): `site_name, tagline, logo_path, mission, vision, values, history, about_image_path, phone, whatsapp, email, address, schedule, map_embed_url, facebook_url, instagram_url, tiktok_url, youtube_url, privacy_notice, seo_description, notify_email`.

- [ ] **Step 1: Tests que fallan**

```php
it('devuelve el default cuando la clave no existe', fn () =>
    expect(setting('mission', 'x'))->toBe('x'));

it('guarda y lee un valor invalidando la cache', function () {
    setting('mission'); // calienta cache
    app(Settings::class)->set('mission', 'Nueva misión');
    expect(setting('mission'))->toBe('Nueva misión');
});

it('usa el logo de marca cuando no hay logo subido', fn () =>
    expect(app(Settings::class)->imageUrl('logo_path', 'images/brand/logo.png'))
        ->toEndWith('images/brand/logo.png'));

it('la home muestra el lema guardado', function () {
    app(Settings::class)->set('tagline', 'Lema de prueba');
    $this->get('/')->assertSee('Lema de prueba');
});
```

- [ ] **Step 2: Correr y verlos fallar** — `./vendor/bin/pest tests/Feature/SettingsTest.php` → FAIL.
- [ ] **Step 3: Implementar** migración, modelo, servicio, helper. `SettingsSeeder` usa `firstOrCreate` (no pisa ediciones) con: `site_name="LyK Asesoría y Consultoría de Pensiones y AFORE"`, `tagline="Hacemos de tu retiro una nueva oportunidad"`, textos de misión/visión/valores/historia de ejemplo marcados para reemplazar, `privacy_notice` con plantilla básica LFPDPPP. Header usa `imageUrl('logo_path', 'images/brand/logo.png')`; home usa `setting('tagline')`.
- [ ] **Step 4: Correr y verlos pasar** — PASS (incluye `HomeTest`).
- [ ] **Step 5: Commit** — `git commit -am "feat: ajustes clave-valor con cache y respaldo de logo"`

---

### Task 3: Login del administrador

**Files:**
- Create: `app/Http/Controllers/Admin/AuthController.php`, `app/Http/Controllers/Admin/PasswordController.php`, `app/Http/Controllers/Admin/DashboardController.php`, `app/Console/Commands/CreateAdmin.php`, `resources/views/layouts/admin.blade.php`, `resources/views/admin/auth/login.blade.php`, `resources/views/admin/password.blade.php`, `resources/views/admin/dashboard.blade.php`, `routes/admin.php`
- Modify: `bootstrap/app.php` (cargar `routes/admin.php` con prefijo `admin`, nombre `admin.`; `redirectGuestsTo(fn () => route('admin.login'))`)
- Test: `tests/Feature/Admin/AuthTest.php`

**Interfaces:**
- Produces: rutas `admin.login` (GET/POST `/admin/login`), `admin.logout` (POST), `admin.dashboard` (GET `/admin`), `admin.password.edit`/`admin.password.update` (GET/PUT `/admin/contrasena`). Grupo con middleware `auth` que usan todas las tareas admin siguientes. Layout `layouts.admin` con menú lateral: Inicio, Ajustes, Portadas, Posts, Servicios, Preguntas, Testimonios, Mensajes (con contador de no leídos, variable compartida `$unreadMessages` — 0 hasta Task 8), Mi contraseña, Salir.
- Comando: `php artisan lyk:admin {email} {--name=Administrador}` pide contraseña (oculta, mín. 8) y crea o actualiza el usuario.

- [ ] **Step 1: Tests que fallan**

```php
it('redirige a invitados al login', fn () =>
    $this->get('/admin')->assertRedirect('/admin/login'));

it('inicia sesión con credenciales válidas', function () {
    $u = User::factory()->create(['password' => 'secreto123']);
    $this->post('/admin/login', ['email' => $u->email, 'password' => 'secreto123'])
        ->assertRedirect('/admin');
    $this->assertAuthenticatedAs($u);
});

it('rechaza contraseña incorrecta', function () {
    $u = User::factory()->create();
    $this->post('/admin/login', ['email' => $u->email, 'password' => 'mal'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('bloquea al sexto intento en un minuto', function () {
    $u = User::factory()->create();
    foreach (range(1, 5) as $_) $this->post('/admin/login', ['email' => $u->email, 'password' => 'mal']);
    $this->post('/admin/login', ['email' => $u->email, 'password' => 'mal'])
        ->assertSessionHasErrors(['email' => fn ($m) => str_contains($m, 'Demasiados intentos')]);
});

it('no existe registro público', fn () => $this->get('/register')->assertNotFound());

it('cambia la contraseña verificando la actual', function () {
    $u = User::factory()->create(['password' => 'secreto123']);
    $this->actingAs($u)->put('/admin/contrasena', [
        'current_password' => 'secreto123', 'password' => 'nueva12345', 'password_confirmation' => 'nueva12345',
    ])->assertSessionHasNoErrors();
    expect(Hash::check('nueva12345', $u->fresh()->password))->toBeTrue();
});

it('crea un admin por comando', function () {
    $this->artisan('lyk:admin', ['email' => 'a@lyk.mx'])
        ->expectsQuestion('Contraseña', 'secreto123')->assertSuccessful();
    expect(User::where('email', 'a@lyk.mx')->exists())->toBeTrue();
});
```

- [ ] **Step 2: Correr y verlos fallar** — `./vendor/bin/pest tests/Feature/Admin/AuthTest.php` → FAIL.
- [ ] **Step 3: Implementar.** Límite con `RateLimiter` clave `strtolower(email).'|'.ip`, 5 intentos / 60 s; mensaje `"Demasiados intentos. Intenta de nuevo en :seconds segundos."`. `session()->regenerate()` al entrar; invalidar sesión al salir.
- [ ] **Step 4: Correr y verlos pasar** — PASS.
- [ ] **Step 5: Commit** — `git commit -am "feat: login de administrador con límite de intentos"`

---

### Task 4: Subida de imágenes + pantalla de Ajustes

**Files:**
- Create: `app/Support/ImageUploader.php`, `app/Http/Controllers/Admin/SettingsController.php`, `app/Http/Requests/Admin/UpdateSettingsRequest.php`, `resources/views/admin/settings/edit.blade.php`, `resources/views/components/admin/image-field.blade.php`
- Test: `tests/Feature/Admin/SettingsTest.php`

**Interfaces:**
- Consumes: `Settings::setMany`, `Settings::KEYS` (Task 2).
- Produces:
  - `ImageUploader::RULES = ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120']`
  - `ImageUploader::store(UploadedFile $file, string $folder): string` → ruta en disco `public`.
  - `ImageUploader::replace(?string $oldPath, UploadedFile $file, string $folder): string` → guarda el nuevo y borra el viejo del disco `public` si existe.
  - `ImageUploader::delete(?string $path): void`.
  - Componente `<x-admin.image-field name="" label="" :current-url="" />` (vista previa + input file + texto "JPG, PNG o WEBP, máx. 5 MB").
  - Rutas `admin.settings.edit` (GET `/admin/ajustes`), `admin.settings.update` (PUT).

- [ ] **Step 1: Tests que fallan** (con `Storage::fake('public')` y usuario autenticado)

```php
it('actualiza misión, visión y contacto', function () {
    $this->put('/admin/ajustes', validSettings(['mission' => 'M', 'vision' => 'V', 'phone' => '55 1234 5678']))
        ->assertRedirect('/admin/ajustes')->assertSessionHas('status');
    expect(setting('mission'))->toBe('M')->and(setting('phone'))->toBe('55 1234 5678');
});

it('cambia el logo y borra el anterior', function () {
    $this->put('/admin/ajustes', validSettings(['logo' => UploadedFile::fake()->image('a.png')]));
    $old = setting('logo_path');
    $this->put('/admin/ajustes', validSettings(['logo' => UploadedFile::fake()->image('b.png')]));
    Storage::disk('public')->assertMissing($old)->assertExists(setting('logo_path'));
});

it('rechaza imágenes de más de 5 MB o HEIC con error en español', function () {
    $this->put('/admin/ajustes', validSettings(['logo' => UploadedFile::fake()->create('f.heic', 12000)]))
        ->assertSessionHasErrors('logo');
    expect(setting('logo_path'))->toBeNull();
    expect(session('errors')->first('logo'))->toStartWith('El campo logo'); // traducción es activa
});

it('valida email y URLs de redes', fn () =>
    $this->put('/admin/ajustes', validSettings(['email' => 'no-es-email', 'facebook_url' => 'abc']))
        ->assertSessionHasErrors(['email', 'facebook_url']));
```
`validSettings(array $overrides = []): array` es un helper en `tests/Pest.php` con valores válidos para todos los campos requeridos (`site_name`, `tagline`, `phone`, `email`).

- [ ] **Step 2: Correr y verlos fallar** → FAIL.
- [ ] **Step 3: Implementar.** Reglas: `site_name, tagline, phone, email` requeridos; `email, notify_email` email; `*_url` nullable url; `whatsapp` nullable regex `/^\+?\d{10,13}$/`; `logo`, `about_image` `nullable` + `ImageUploader::RULES`; textos largos `nullable|string|max:5000` (`privacy_notice` max 20000). Archivos a carpetas `branding/`. La pantalla se agrupa en pestañas/secciones: Identidad, Nosotros, Contacto, Redes, Aviso y SEO. Atributos traducidos en `lang/es/validation.php` → `attributes` (p. ej. `logo => 'logo'`, `about_image => 'foto de nosotros'`).
- [ ] **Step 4: Correr y verlos pasar** → PASS.
- [ ] **Step 5: Commit** — `git commit -am "feat: ajustes editables con subida de logo e imágenes"`

---

### Task 5: Contenido ordenable — Portadas, Servicios, Preguntas, Testimonios

**Files:**
- Create: `app/Models/Concerns/Orderable.php`; migraciones y modelos `HeroSlide`, `Service`, `Faq`, `Testimonial` (con factories); controladores `Admin/HeroSlideController`, `Admin/ServiceController`, `Admin/FaqController`, `Admin/TestimonialController`; vistas `admin/{slides,services,faqs,testimonials}/{index,form}.blade.php`; `resources/views/components/icon.blade.php`
- Modify: `routes/admin.php`
- Test: `tests/Feature/Admin/OrderableContentTest.php`, `tests/Unit/OrderableTest.php`

**Interfaces:**
- Consumes: `ImageUploader` (Task 4), grupo admin (Task 3).
- Produces:
  - Trait `Orderable`: columnas `sort_order int default 0`, `is_active bool default true`; `scopeActive($q)` → `where('is_active', true)->orderBy('sort_order')->orderBy('id')`.
  - `hero_slides(image_path, title?, subtitle?, button_text?, button_url?)`; `services(title, description text, icon string)`; `faqs(question, answer text)`; `testimonials(name, quote text, location?, photo_path?)`.
  - `Service::ICONS = ['piggy-bank', 'document', 'calendar', 'shield', 'users', 'scale', 'heart']`; `<x-icon name="" class="" />` renderiza SVG inline para cada uno.
  - `Route::resource(..)->except('show')` con nombres `admin.slides.*` (`/admin/portadas`), `admin.services.*` (`/admin/servicios`), `admin.faqs.*` (`/admin/preguntas`), `admin.testimonials.*` (`/admin/testimonios`). Al borrar se borra la imagen con `ImageUploader::delete`.

- [ ] **Step 1: Tests que fallan**

```php
it('active() excluye inactivos y ordena por sort_order', function () {
    Faq::factory()->create(['question' => 'B', 'sort_order' => 2]);
    Faq::factory()->create(['question' => 'A', 'sort_order' => 1]);
    Faq::factory()->create(['question' => 'X', 'is_active' => false]);
    expect(Faq::active()->pluck('question')->all())->toBe(['A', 'B']);
});

it('crea una portada con imagen', function () {
    $this->post('/admin/portadas', ['image' => UploadedFile::fake()->image('p.jpg', 1600, 900), 'title' => 'Hola', 'sort_order' => 1, 'is_active' => 1])
        ->assertRedirect('/admin/portadas');
    Storage::disk('public')->assertExists(HeroSlide::first()->image_path);
});

it('exige imagen al crear portada pero no al editar', function () {
    $this->post('/admin/portadas', ['title' => 'x'])->assertSessionHasErrors('image');
    $s = HeroSlide::factory()->create();
    $this->put("/admin/portadas/{$s->id}", ['title' => 'y', 'sort_order' => 0])->assertSessionHasNoErrors();
});

it('borrar una portada borra su archivo', function () { /* create via POST, delete, assertMissing */ });

it('rechaza un icono de servicio fuera de la lista', fn () =>
    $this->post('/admin/servicios', ['title' => 'T', 'description' => 'D', 'icon' => 'rocket'])
        ->assertSessionHasErrors('icon'));

it('CRUD de preguntas y testimonios', function () { /* store + update + destroy, assertDatabaseHas/Missing */ });
```

- [ ] **Step 2: Correr y verlos fallar** → FAIL.
- [ ] **Step 3: Implementar** trait, migraciones, modelos, controladores y vistas. `button_url` nullable url o ruta que empiece con `/`. Formularios con checkbox `is_active` (ausente = false) y campo numérico `sort_order`. Índices en tabla con miniatura, orden, estado y acciones Editar/Borrar (borrar con `confirm()`).
- [ ] **Step 4: Correr y verlos pasar** → PASS.
- [ ] **Step 5: Commit** — `git commit -am "feat: portadas, servicios, preguntas y testimonios administrables"`

---

### Task 6: Posts (admin)

**Files:**
- Create: migración + `app/Models/Post.php` + `PostFactory`, `app/Http/Controllers/Admin/PostController.php`, `app/Http/Requests/Admin/PostRequest.php`, `resources/views/admin/posts/{index,form}.blade.php`, `resources/js/admin.js` (Trix)
- Modify: `config/purifier.php` (perfil `post`), `vite.config.js` (entrada `resources/js/admin.js`), `routes/admin.php`
- Test: `tests/Feature/Admin/PostTest.php`

**Interfaces:**
- Consumes: `ImageUploader` (Task 4).
- Produces:
  - `posts(title, slug unique, excerpt string(300)?, body longtext, cover_path?, status enum('draft','published') default 'draft', published_at datetime?)`.
  - `Post::scopePublished($q)` → `status = published` y `published_at <= now()`, orden `published_at desc`. `getRouteKeyName(): 'slug'`.
  - Slug automático en `creating` (y al cambiar título sólo si está en borrador): `Str::slug(title)`, con sufijo `-2`, `-3`… si existe.
  - `Post::excerptText(): string` → `excerpt` o los primeros 160 caracteres de `strip_tags(body)`.
  - Rutas `admin.posts.*` en `/admin/posts` (resource sin `show`), el admin edita por `id` (`{post:id}`).

- [ ] **Step 1: Tests que fallan**

```php
it('crea un post publicado con portada', function () {
    $this->post('/admin/posts', ['title' => 'Modalidad 40', 'body' => '<p>Hola</p>', 'status' => 'published',
        'published_at' => now()->format('Y-m-d\TH:i'), 'cover' => UploadedFile::fake()->image('c.jpg')])
        ->assertRedirect('/admin/posts');
    expect(Post::first())->slug->toBe('modalidad-40')->cover_path->not->toBeNull();
});

it('genera slugs únicos', function () {
    Post::factory()->create(['title' => 'Modalidad 40']);
    expect(Post::factory()->create(['title' => 'Modalidad 40'])->slug)->toBe('modalidad-40-2');
});

it('sanitiza HTML peligroso', function () {
    $this->post('/admin/posts', ['title' => 'X', 'status' => 'draft',
        'body' => '<p>ok</p><script>alert(1)</script><img src=x onerror=alert(1)>']);
    expect(Post::first()->body)->toContain('<p>ok</p>')->not->toContain('script')->not->toContain('onerror');
});

it('published() excluye borradores y fechas futuras', function () {
    Post::factory()->create(['status' => 'draft']);
    Post::factory()->create(['status' => 'published', 'published_at' => now()->addDay()]);
    $ok = Post::factory()->create(['status' => 'published', 'published_at' => now()->subDay()]);
    expect(Post::published()->pluck('id')->all())->toBe([$ok->id]);
});

it('si se publica sin fecha usa ahora', function () { /* published_at null + status published → not null */ });
```

- [ ] **Step 2: Correr y verlos fallar** → FAIL.
- [ ] **Step 3: Implementar.** Sanitizar con `clean($body, 'post')`; perfil permite `p,br,strong,em,a[href],ul,ol,li,h2,h3,blockquote,figure,figcaption,img[src|alt]`. Trix con adjuntos deshabilitados (`trix-file-accept` → `preventDefault()`), el `<input type=hidden name=body>` lo alimenta. Lista admin con filtro por estado y búsqueda por título.
- [ ] **Step 4: Correr y verlos pasar** → PASS.
- [ ] **Step 5: Commit** — `git commit -am "feat: administración de posts con editor y sanitizado"`

---

### Task 7: Páginas públicas — Home, Posts, Nosotros, Aviso

**Files:**
- Create: `app/Http/Controllers/HomeController.php`, `PostController.php`, `PageController.php`; vistas `home.blade.php` (reemplaza la provisional), `posts/{index,show}.blade.php`, `pages/{about,privacy}.blade.php`, `partials/{hero,services,stats,latest-posts,testimonials,faqs,cta}.blade.php`, `resources/js/hero.js`
- Modify: `routes/web.php`
- Test: `tests/Feature/PublicPagesTest.php`

**Interfaces:**
- Consumes: `Settings`, `HeroSlide/Service/Faq/Testimonial::active()`, `Post::published()`, `Post::excerptText()`.
- Produces: rutas `home`, `posts.index` (`/posts`), `posts.show` (`/posts/{post:slug}`), `about` (`/nosotros`), `privacy` (`/aviso-de-privacidad`).

- [ ] **Step 1: Tests que fallan**

```php
it('home muestra portadas, servicios, últimos 3 posts, testimonios y FAQ activos', function () {
    HeroSlide::factory()->create(['title' => 'Portada A']);
    Service::factory()->create(['title' => 'Modalidad 40']);
    Post::factory()->published()->count(4)->sequence(fn ($s) => ['title' => "Post {$s->index}"])->create();
    Faq::factory()->create(['question' => '¿Qué es la AFORE?']);
    Testimonial::factory()->create(['name' => 'Doña Rosa']);
    $this->get('/')->assertOk()->assertSee(['Portada A', 'Modalidad 40', '¿Qué es la AFORE?', 'Doña Rosa'])
        ->assertViewHas('posts', fn ($p) => $p->count() === 3);
});

it('home sin contenido usa la portada de marca y oculta secciones vacías', fn () =>
    $this->get('/')->assertOk()->assertSee('images/brand/portada.jpg')
        ->assertDontSee('Preguntas frecuentes')->assertDontSee('Lo que dicen'));

it('lista posts paginados de 9 en 9', function () {
    Post::factory()->published()->count(10)->create();
    $this->get('/posts')->assertViewHas('posts', fn ($p) => $p->count() === 9 && $p->total() === 10);
});

it('no muestra borradores', function () {
    $d = Post::factory()->create(['status' => 'draft']);
    $this->get("/posts/{$d->slug}")->assertNotFound();
});

it('nosotros muestra misión y visión editables', function () {
    app(Settings::class)->setMany(['mission' => 'Misión X', 'vision' => 'Visión Y']);
    $this->get('/nosotros')->assertSee(['Misión X', 'Visión Y']);
});

it('aviso de privacidad muestra el texto guardado', function () { /* set privacy_notice, assertSee */ });
```
(Añadir `published()` state a `PostFactory`.)

- [ ] **Step 2: Correr y verlos fallar** → FAIL.
- [ ] **Step 3: Implementar.** Hero: carrusel CSS scroll-snap con autoplay en `hero.js` (pausa con `prefers-reduced-motion`), 1 slide = sin controles. Secciones: servicios en tarjetas con `<x-icon>`; "¿Por qué LyK?" con 3 cifras fijas en la vista; testimonios; FAQ con `<details>`; CTA final a WhatsApp/contacto. Post show: cuerpo con `{!! $post->body !!}` (ya sanitizado), botones compartir `https://wa.me/?text=` y `https://www.facebook.com/sharer/sharer.php?u=`. Textos multilínea de ajustes con `nl2br(e(...))`.
- [ ] **Step 4: Correr y verlos pasar** → PASS.
- [ ] **Step 5: Commit** — `git commit -am "feat: páginas públicas home, posts, nosotros y aviso"`

---

### Task 8: Contacto + bandeja de mensajes

**Files:**
- Create: migración + `app/Models/ContactMessage.php` + factory, `app/Http/Controllers/ContactController.php`, `app/Http/Controllers/Admin/MessageController.php`, `app/Mail/NewContactMessage.php`, vistas `pages/contact.blade.php`, `mail/new-contact-message.blade.php`, `admin/messages/{index,show}.blade.php`
- Modify: `routes/web.php`, `routes/admin.php`, `app/Providers/AppServiceProvider.php` (compartir `$unreadMessages` con `layouts.admin`)
- Test: `tests/Feature/ContactTest.php`, `tests/Feature/Admin/MessageTest.php`

**Interfaces:**
- Produces: `contact_messages(name, phone, email?, topic?, message text, read_at?)`; `ContactMessage::scopeUnread`; rutas `contact` (GET `/contacto`), `contact.store` (POST, `throttle:5,1`), `admin.messages.index|show|destroy` (`/admin/mensajes`), `admin.messages.toggle` (PATCH, marca/desmarca atendido). `show` marca `read_at` si es null.

- [ ] **Step 1: Tests que fallan**

```php
it('guarda el mensaje y avisa por correo', function () {
    Mail::fake(); app(Settings::class)->set('notify_email', 'dueno@lyk.mx');
    $this->post('/contacto', validContact())->assertRedirect('/contacto')->assertSessionHas('status');
    expect(ContactMessage::count())->toBe(1);
    Mail::assertSent(NewContactMessage::class, fn ($m) => $m->hasTo('dueno@lyk.mx'));
});

it('si el correo falla el mensaje queda guardado', function () {
    Mail::shouldReceive('to')->andThrow(new \RuntimeException('smtp'));
    $this->post('/contacto', validContact())->assertSessionHas('status');
    expect(ContactMessage::count())->toBe(1);
});

it('ignora en silencio el honeypot', function () {
    $this->post('/contacto', validContact(['website' => 'spam.com']))->assertSessionHas('status');
    expect(ContactMessage::count())->toBe(0);
});

it('exige aceptar el aviso de privacidad', fn () =>
    $this->post('/contacto', validContact(['privacy' => null]))->assertSessionHasErrors('privacy'));

it('limita a 5 envíos por minuto', function () {
    foreach (range(1, 5) as $_) $this->post('/contacto', validContact());
    $this->post('/contacto', validContact())->assertStatus(429);
});

it('abrir un mensaje lo marca como leído', function () { /* actingAs, get show, read_at not null */ });
```
`validContact(array $o = []): array` en `tests/Pest.php`: `name, phone (10 dígitos), topic, message, privacy => '1'`.

- [ ] **Step 2: Correr y verlos fallar** → FAIL.
- [ ] **Step 3: Implementar.** Guardar primero, luego enviar el correo dentro de `try/catch` con `report($e)`. Destino: `setting('notify_email') ?: setting('email')`. Temas del select: Modalidad 40, Pensión Ley 73, Pensión Ley 97, AFORE, Otro. La página muestra teléfono (`tel:`), WhatsApp, email, dirección, horario e `<iframe>` de `map_embed_url` sólo si existe. Respuesta 429 con vista amable en español (`resources/views/errors/429.blade.php`).
- [ ] **Step 4: Correr y verlos pasar** → PASS.
- [ ] **Step 5: Commit** — `git commit -am "feat: formulario de contacto con bandeja y aviso por correo"`

---

### Task 9: WhatsApp flotante, SEO y sitemap

**Files:**
- Create: `resources/views/partials/{whatsapp-button,seo}.blade.php`, `app/Http/Controllers/SitemapController.php`, `public/robots.txt` (modificar)
- Modify: `layouts/public.blade.php`, `posts/show.blade.php`, `routes/web.php`
- Test: `tests/Feature/SeoTest.php`

**Interfaces:**
- Produces: `sitemap` (GET `/sitemap.xml`); parcial `seo` acepta `title`, `description`, `image` (por defecto `site_name`, `seo_description`, portada de marca).

- [ ] **Step 1: Tests que fallan**

```php
it('muestra el botón de WhatsApp sólo si hay número', function () {
    $this->get('/')->assertDontSee('wa.me/');
    app(Settings::class)->set('whatsapp', '+525512345678');
    $this->get('/')->assertSee('https://wa.me/525512345678?text=', false);
});

it('el post tiene metas Open Graph con su portada', function () {
    $p = Post::factory()->published()->create(['title' => 'Guía M40', 'cover_path' => 'posts/c.jpg']);
    $this->get("/posts/{$p->slug}")
        ->assertSee('<meta property="og:title" content="Guía M40', false)
        ->assertSee('posts/c.jpg', false);
});

it('sitemap incluye páginas y posts publicados', function () {
    $p = Post::factory()->published()->create();
    $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml')
        ->assertSee(route('posts.show', $p))->assertSee(route('about'));
});
```

- [ ] **Step 2: Correr y verlos fallar** → FAIL.
- [ ] **Step 3: Implementar.** Mensaje prellenado: "Hola, me gustaría recibir asesoría sobre mi pensión." (url-encoded). Número: sólo dígitos. Botón fijo abajo-derecha, 56px, `aria-label="Escríbenos por WhatsApp"`. `robots.txt` → `Disallow: /admin` y `Sitemap:`.
- [ ] **Step 4: Correr y verlos pasar** → PASS.
- [ ] **Step 5: Commit** — `git commit -am "feat: botón de WhatsApp, metas SEO y sitemap"`

---

### Task 10: Contenido inicial y puesta en marcha

**Files:**
- Create: `database/seeders/ContentSeeder.php`, `database/seeders/assets/` (copia de `recursos/855623971_…jpg`, `861321894_…jpg`, `873330435_…jpg`, `493311514_…jpg`), `README.md`
- Modify: `database/seeders/DatabaseSeeder.php`
- Test: `tests/Feature/ContentSeederTest.php`

**Interfaces:**
- Consumes: todos los modelos anteriores.

- [ ] **Step 1: Test que falla**

```php
it('siembra contenido inicial sin duplicar al repetirse', function () {
    Storage::fake('public');
    $this->seed(DatabaseSeeder::class); $this->seed(DatabaseSeeder::class);
    expect(Service::count())->toBe(7)
        ->and(Post::published()->count())->toBe(3)
        ->and(HeroSlide::count())->toBe(1)
        ->and(Faq::count())->toBeGreaterThanOrEqual(4);
    Storage::disk('public')->assertExists(HeroSlide::first()->image_path);
});
```

- [ ] **Step 2: Correr y verlo fallar** → FAIL.
- [ ] **Step 3: Implementar** `ContentSeeder` idempotente (`firstOrCreate` por título/slug):
  - 1 portada con `493311514_…jpg`, título vacío (la imagen ya trae texto).
  - 7 servicios del brief §6 con iconos de `Service::ICONS`.
  - 3 posts publicados con las infografías como portada: "Modalidad 40: qué es y qué cambia para nuevos inscritos", "Modalidad 40: ¿qué cambia para ti? Medidas temporales del IMSS", "¿Ya pagas Modalidad 40? Qué revisar para continuar con tu plan de pensión"; cuerpo = imagen + resumen en texto del contenido de la infografía + fuente (DOF 21/10/2026, Acuerdo ACDO.AS2.HCT.101026/302.P.DG).
  - 4 FAQ (¿Qué es la Modalidad 40?, ¿Cómo sé si soy Ley 73 o Ley 97?, ¿Puedo retirar dinero de mi AFORE?, ¿Cuánto cuesta la asesoría?) con respuestas de ejemplo marcadas para revisar.
  - Sin testimonios (no inventar opiniones de clientes reales).
- [ ] **Step 4: Correr toda la suite** — `./vendor/bin/pest` → todo PASS. Luego `npm run build` sin errores.
- [ ] **Step 5: README** con: crear BD `lyk`, `cp .env.example .env && php artisan key:generate`, `php artisan migrate --seed`, `php artisan storage:link`, `php artisan lyk:admin correo@dominio`, `npm run build`, VirtualHost de Apache con `DocumentRoot /var/www/html/lyk/public` y `AllowOverride All`, permisos de escritura en `storage/` y `bootstrap/cache/`, configurar `MAIL_*`.
- [ ] **Step 6: Verificación manual** — `php artisan serve`, recorrer en 360px y escritorio: home, posts, nosotros, contacto, aviso; entrar a `/admin`, cambiar logo, misión y una portada, y confirmar los cambios en el sitio público.
- [ ] **Step 7: Commit** — `git add -A && git commit -m "feat: contenido inicial de LyK y guía de instalación"`
