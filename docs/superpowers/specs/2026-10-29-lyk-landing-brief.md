# Brief — Landing page LyK Asesoría y Consultoría de Pensiones y AFORE

**Fecha:** 2026-10-29
**Recursos:** `./recursos/` (logo `logo-removebg-preview.png` / `logo.jpg`, portada `493311514_…jpg`, infografías Modalidad 40 `855623971_…jpg`, `861321894_…jpg`, `873330435_…jpg`)

## 1. La empresa

LyK (L&K) asesora a personas en su retiro: pensiones IMSS (Ley 73 / Ley 97), Modalidad 40, AFORE, y trámites relacionados. Público principal: adultos de 45–70 años en México, muchos con poca experiencia digital, que llegan desde Facebook/WhatsApp. Tono: cercano, confiable y claro, sin tecnicismos.

**Lema (de la portada):** "Hacemos de tu retiro una nueva oportunidad".

## 2. Objetivos

1. Generar contactos (WhatsApp, llamada, formulario) de personas que necesitan asesoría.
2. Dar autoridad: publicar guías e infografías (como las de Modalidad 40) en un blog.
3. Que el dueño administre todo el contenido sin programador.

## 3. Identidad visual

| Token | Valor | Origen |
|---|---|---|
| `navy` | `#1B2A4A` | letras L&K y tronco |
| `leaf` | `#5B9A3C` | hojas |
| `gold` | `#E3A92B` | raíces doradas |
| `sage` | `#B9CDB0` | fondo de la portada |
| `cream` | `#F7F5EF` | fondo general |

- Títulos: serif (**Libre Baskerville**), como el logo. Texto: **Source Sans 3**. Ambas de Google Fonts.
- Texto base 18px (público mayor), botones grandes (mín. 48px de alto), contraste AA.
- Fotos de personas reales/mayores sonriendo, mucho aire, tarjetas redondeadas.

## 4. Sitio público

| Página | Ruta | Contenido |
|---|---|---|
| Home | `/` | Carrusel de portadas (editable), servicios, "¿Por qué LyK?" (cifras), últimos 3 posts, testimonios, preguntas frecuentes, llamada a la acción a WhatsApp/contacto |
| Posts | `/posts`, `/posts/{slug}` | Lista paginada (9 por página) con imagen, fecha y extracto; detalle con imagen, cuerpo y botones de compartir (WhatsApp, Facebook) |
| Acerca de nosotros | `/nosotros` | Historia, misión, visión, valores (todo editable) y foto del equipo |
| Contacto | `/contacto` | Formulario (nombre, teléfono, email opcional, tema, mensaje, aceptación del aviso), teléfono, WhatsApp, email, dirección, horario y mapa de Google embebido |
| Aviso de privacidad | `/aviso-de-privacidad` | Texto editable (obligatorio por la LFPDPPP al recolectar datos) |

Elementos globales: header con logo y menú (hamburguesa en móvil), botón flotante de WhatsApp, footer con contacto, redes sociales y enlace al aviso.

## 5. Panel de administración (`/admin`)

Acceso sólo con inicio de sesión (sin registro público). Funciones:

- **Ajustes generales:** cambiar logo, nombre, lema, misión, visión, valores, historia, foto de "nosotros", teléfono, WhatsApp, email, dirección, horario, URL de mapa, redes sociales (Facebook, Instagram, TikTok, YouTube), texto del aviso de privacidad, descripción SEO.
- **Portadas:** agregar, cambiar, reordenar, activar/desactivar fotos del carrusel con título, subtítulo y botón opcionales.
- **Posts:** crear/editar/borrar con editor visual, imagen de portada, borrador o publicado, fecha de publicación.
- **Servicios, Preguntas frecuentes, Testimonios:** listas editables con orden y activo/inactivo.
- **Mensajes:** bandeja de los formularios de contacto, marcar como atendido, borrar.
- **Mi cuenta:** cambiar contraseña.

## 6. Ideas adicionales

Incluidas en esta versión (ya en las secciones de arriba):
- Servicios sugeridos para la carga inicial: Modalidad 40, Pensión Ley 73, Pensión Ley 97, Recuperación de AFORE, Retiro parcial por desempleo/matrimonio, Corrección de semanas cotizadas, Pensión por viudez/orfandad.
- Botón flotante de WhatsApp con mensaje prellenado.
- Bandeja de mensajes + aviso por email al dueño.
- Testimonios y preguntas frecuentes editables.
- Compartir posts por WhatsApp/Facebook (donde vive su público).
- SEO: metas Open Graph para que los posts se vean bien al compartir, `sitemap.xml`.
- Infografías existentes publicadas como primeros posts.

Para una fase 2 (fuera de este plan):
- Calculadora estimada de pensión Ley 73 / Modalidad 40.
- Agenda de citas en línea (Calendly o propia).
- Boletín por email / alertas de cambios legales (como el acuerdo del DOF 21/10/2026).
- Categorías y buscador de posts; galería de infografías descargables.
- Varios usuarios administradores con roles.
- Colores del tema editables desde el panel.
- Chat / integración con WhatsApp Business API.

## 7. Requisitos no funcionales

- Responsivo desde 360px; Lighthouse ≥ 90 en rendimiento y accesibilidad en móvil.
- Imágenes subidas: JPG, PNG o WEBP, máximo 5 MB; mensajes de error en español.
- Idioma `es`, zona horaria `America/Mexico_City`.
- Seguridad: CSRF, límite de intentos de login, HTML de posts sanitizado, formulario con anti-spam.
