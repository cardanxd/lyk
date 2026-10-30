# LyK Asesoría y Consultoría de Pensiones y AFORE

Sitio web de LyK (inicio, posts, nosotros, contacto y aviso de privacidad) con un panel de administración en `/admin`.

Hecho con Laravel 13, Blade, Tailwind CSS 4 y MySQL.

## Instalación

```bash
composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

Crea la base de datos y configura `.env`:

```sql
CREATE DATABASE lyk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```dotenv
APP_URL=https://tu-dominio.mx
DB_CONNECTION=mysql
DB_DATABASE=lyk
DB_USERNAME=...
DB_PASSWORD=...

# Correo para recibir avisos de mensajes de contacto
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=no-responder@tu-dominio.mx
```

```bash
php artisan migrate --seed      # tablas + contenido inicial (servicios, 3 posts, preguntas)
php artisan storage:link        # para que se vean las imágenes subidas
php artisan lyk:admin correo@dominio.mx --name="Tu nombre"   # crea el usuario del panel (pide contraseña)
```

`lyk:admin` también sirve para restablecer la contraseña de un usuario existente.

## Apache

```apache
<VirtualHost *:80>
    ServerName tu-dominio.mx
    DocumentRoot /var/www/html/lyk/public

    <Directory /var/www/html/lyk/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Requiere `a2enmod rewrite`. El usuario de Apache (`www-data`) debe poder escribir en `storage/` y `bootstrap/cache/`:

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache
```

### Requisitos de PHP

Para subir fotos de hasta 5 MB y videos cortos de hasta 50 MB (por ejemplo, desde el celular):

```ini
upload_max_filesize = 60M
post_max_size = 128M
```

Con PHP-FPM ya vienen en `public/.user.ini`. Con `mod_php` agrégalos al `php.ini` de Apache (`/etc/php/8.4/apache2/php.ini`) y reinicia Apache. Si un archivo pesa más, el panel muestra un aviso en español con un botón para volver al formulario.

`MAIL_TIMEOUT=10` evita que el formulario de contacto se quede esperando si el servidor de correo no responde. Con `MAIL_MAILER=log` no se envían correos (sólo se registran).

En producción: `APP_ENV=production`, `APP_DEBUG=false` y `php artisan optimize`.

## Uso del panel

Entra a `/admin/login`. Desde ahí puedes:

- **Ajustes:** logo, nombre, lema, misión, visión, valores, historia, foto de "Nosotros", teléfono, WhatsApp, correo, dirección, horario, mapa, redes sociales, aviso de privacidad y descripción para Google.
- **Sucursales:** nombre, dirección, ciudad, horario, teléfono, WhatsApp, foto de la fachada y mapa. El sitio muestra cuántas sucursales hay en `/sucursales`, en el inicio y en contacto.
- **Portadas:** fotos del carrusel de inicio (orden, visible/oculto, título y botón opcionales).
- **Posts:** guías y noticias con editor, imagen de portada, borrador o publicado y fecha programada. Cada post puede tener una galería de fotos (hasta 10 por envío), videos cortos (MP4, MOV o WEBM de hasta 50 MB) y enlaces de YouTube o Facebook.
- **Servicios, Preguntas y Testimonios:** listas con orden y visible/oculto.
- **Mensajes:** lo que llega del formulario de contacto. También se avisa por correo.

Las imágenes aceptan JPG, PNG o WEBP de hasta 5 MB.

**Pendiente después de instalar:** reemplaza los textos marcados con `[Reemplazar]` y `[Revisar]` (misión, visión, historia, aviso de privacidad y respuestas de preguntas frecuentes), y agrega el WhatsApp para que aparezca el botón flotante.

Para Google: da de alta `https://tu-dominio.mx/sitemap.xml` en Google Search Console.

## Desarrollo

```bash
composer run dev     # servidor + Vite
./vendor/bin/pest    # pruebas
```
