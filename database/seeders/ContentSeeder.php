<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Contenido inicial del sitio. Es idempotente: no duplica ni pisa lo que el dueño ya editó.
 * Los textos marcados con [Revisar] son de ejemplo.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->slides();
        $this->services();
        $this->posts();
        $this->faqs();
    }

    private function slides(): void
    {
        if (HeroSlide::exists()) {
            return;
        }

        HeroSlide::create(['image_path' => $this->asset('portada.jpg', 'slides'), 'sort_order' => 0]);
    }

    private function services(): void
    {
        $services = [
            ['Modalidad 40', 'Te ayudamos a evaluar si te conviene seguir cotizando por tu cuenta, cuánto pagar y por cuánto tiempo.', 'calendar'],
            ['Pensión Ley 73', 'Calculamos tu pensión estimada y revisamos tus semanas y tu salario promedio de las últimas 250 semanas.', 'document'],
            ['Pensión Ley 97', 'Te orientamos sobre tu cuenta individual, renta vitalicia o retiro programado.', 'scale'],
            ['Recuperación de AFORE', 'Te acompañamos para recuperar los recursos de tu AFORE y los de tus beneficiarios.', 'piggy-bank'],
            ['Retiro parcial por desempleo o matrimonio', 'Te decimos si puedes solicitarlo y cómo afecta tus semanas cotizadas.', 'heart'],
            ['Corrección de semanas cotizadas', 'Revisamos tu historial laboral del IMSS y te ayudamos a corregir semanas o salarios que no aparecen.', 'shield'],
            ['Pensión por viudez u orfandad', 'Te guiamos en el trámite de pensión para los beneficiarios de un asegurado.', 'users'],
        ];

        foreach ($services as $i => [$title, $description, $icon]) {
            Service::firstOrCreate(['title' => $title], ['description' => $description, 'icon' => $icon, 'sort_order' => $i]);
        }
    }

    private function posts(): void
    {
        $source = '<p><em>Fuentes: IMSS, Continuación Voluntaria; DOF 21/10/2026, Acuerdo ACDO.AS2.HCT.101026/302.P.DG.</em></p>';

        $posts = [
            [
                'slug' => 'modalidad-40-que-es-y-que-cambia-para-nuevos-inscritos',
                'title' => 'Modalidad 40: qué es y qué cambia para nuevos inscritos',
                'excerpt' => 'Guía para quien aún no la contrata: cómo funciona, quién puede solicitarla y las medidas temporales vigentes desde el 22 de octubre de 2026.',
                'image' => 'm40-nuevos-inscritos.jpg',
                'days_ago' => 2,
                'body' => <<<'HTML'
<h2>¿Cómo funciona?</h2>
<p>La Modalidad 40 permite seguir cotizando al IMSS por tu cuenta después de dejar un empleo con patrón:</p>
<ol><li>Dejas de cotizar con patrón.</li><li>Pagas tus propias cuotas.</li><li>Sigues sumando semanas.</li></ol>
<p>No es una pensión automática ni incluye atención médica por sí sola.</p>
<h2>¿Quién puede solicitarla?</h2>
<p>Personas dadas de baja, con al menos 52 semanas cotizadas en los 5 años previos a la baja y dentro del plazo de 5 años para solicitarla.</p>
<h2>Para nuevas inscripciones (medidas temporales vigentes desde el 22 de octubre de 2026)</h2>
<ul>
<li><strong>El salario tiene un nuevo límite:</strong> se toma tu último salario registrado al causar baja, con un aumento permitido de hasta una UMA. Se respeta el piso del salario mínimo general.</li>
<li><strong>El inicio es desde la solicitud:</strong> la nueva inscripción no permite cubrir periodos anteriores de forma retroactiva.</li>
<li><strong>Atención si cotizas como independiente:</strong> quienes estén o se incorporen al esquema del artículo 13, fracción I, no podrán contratar Modalidad 40.</li>
</ul>
<h2>¿Cómo se calcula lo que pagas?</h2>
<p>El IMSS usa tu salario registrado para calcular cuánto debes pagar en Modalidad 40. Ese salario no es la cantidad que recibirás de pensión.</p>
HTML,
            ],
            [
                'slug' => 'modalidad-40-que-cambia-para-ti-medidas-temporales-del-imss',
                'title' => 'Modalidad 40: ¿qué cambia para ti? Medidas temporales del IMSS',
                'excerpt' => 'Publicado en el DOF el 21/10/2026 y vigente desde el 22/10/2026: restricciones, límite salarial, altas sin retroactividad e inscripción digital.',
                'image' => 'm40-que-cambia.jpg',
                'days_ago' => 4,
                'body' => <<<'HTML'
<p>Publicado en el DOF el 21/10/2026 · Vigente desde el 22/10/2026.</p>
<h2>Trabajadores independientes</h2>
<p>Quienes estén o se incorporen al esquema del artículo 13, fracción I, de la Ley del Seguro Social no podrán contratar la Continuación Voluntaria. Atención si cotizas como independiente en Modalidad 10.</p>
<h2>Límite salarial al incorporarse</h2>
<p>Último salario base al causar baja en el régimen obligatorio, o hasta ese importe más una UMA, sin ser inferior al salario mínimo general vigente para el resto del país. Es un límite salarial, no una cuota mensual.</p>
<h2>Nuevas altas sin retroactividad</h2>
<p>El nuevo aseguramiento comienza exclusivamente desde la fecha de solicitud.</p>
<h2>Inscripción digital</h2>
<p>El acuerdo ordena ajustar los sistemas para inscribir únicamente por medios digitales y crear atención para casos especiales.</p>
<h2>¿Ya estás inscrito?</h2>
<p>Se mantienen las condiciones actuales hasta concluir el periodo contratado. Las semanas y salarios efectivamente pagados se reconocen.</p>
HTML,
            ],
            [
                'slug' => 'ya-pagas-modalidad-40-que-revisar-para-continuar-con-tu-plan-de-pension',
                'title' => '¿Ya pagas Modalidad 40? Qué revisar para continuar con tu plan de pensión',
                'excerpt' => 'Si ya estabas inscrito al entrar en vigor el acuerdo, conservas tus condiciones. Estos son los 5 puntos que debes revisar.',
                'image' => 'm40-ya-pagas.jpg',
                'days_ago' => 1,
                'body' => <<<'HTML'
<p>Si ya estabas inscrito al entrar en vigor el acuerdo, conservas tus condiciones hasta concluir el periodo contratado. Las semanas y salarios efectivamente pagados siguen contando.</p>
<ol>
<li><strong>Confirma tu periodo contratado.</strong> Revisa tu inscripción y pide al IMSS confirmar hasta cuándo se mantienen tus condiciones. No supongas que duran hasta que te pensiones.</li>
<li><strong>Mantén tus pagos al corriente.</strong> Revisa el importe y vencimiento de tu línea de captura. La regla general es pagar por adelantado, a más tardar el día 17 de cada mes.</li>
<li><strong>Guarda y verifica.</strong> Conserva tus comprobantes. Revisa que las semanas y el salario aparezcan correctamente en tu historial del IMSS.</li>
<li><strong>Revisa antes de que termine.</strong> Al concluir el periodo contratado, una nueva incorporación queda sujeta a las condiciones del acuerdo. Confirma con el IMSS cómo aplica a tu caso.</li>
<li><strong>Actualiza tu plan de pensión.</strong> Recalcula cuánto puedes aportar, las semanas que te faltan y tu edad de retiro. En Ley 73 también importa el promedio salarial de las últimas 250 semanas cotizadas.</li>
</ol>
<h2>Evita interrumpir tu continuidad</h2>
<p>Dejar de pagar dos meses puede causar la baja. Antes de suspender pagos o cambiar de esquema, revisa cómo afectaría tu plan.</p>
HTML,
            ],
        ];

        foreach ($posts as $post) {
            if (Post::where('slug', $post['slug'])->exists()) {
                continue;
            }

            Post::create([
                'slug' => $post['slug'],
                'title' => $post['title'],
                'excerpt' => $post['excerpt'],
                'body' => clean($post['body'].$source, 'post'),
                'cover_path' => $this->asset($post['image'], 'posts'),
                'status' => 'published',
                'published_at' => now()->subDays($post['days_ago']),
            ]);
        }
    }

    private function faqs(): void
    {
        $faqs = [
            ['¿Qué es la Modalidad 40?', 'Es la continuación voluntaria en el régimen obligatorio del IMSS: te permite seguir cotizando por tu cuenta después de dejar un empleo, para sumar semanas y mejorar tu salario de cotización. [Revisar]'],
            ['¿Cómo sé si soy Ley 73 o Ley 97?', 'Depende de cuándo empezaste a cotizar: si tu primera alta en el IMSS fue antes del 1 de julio de 1997 perteneces a la Ley 73; si fue después, a la Ley 97. [Revisar]'],
            ['¿Puedo retirar dinero de mi AFORE?', 'Sí, en algunos casos: por desempleo, por matrimonio o al pensionarte. Cada retiro tiene requisitos y puede afectar tus semanas cotizadas, por eso conviene revisarlo antes. [Revisar]'],
            ['¿Cuánto cuesta la asesoría?', 'La primera plática es para conocer tu caso. Después te explicamos con claridad el costo del servicio que necesitas. [Revisar con tus precios]'],
        ];

        foreach ($faqs as $i => [$question, $answer]) {
            Faq::firstOrCreate(['question' => $question], ['answer' => $answer, 'sort_order' => $i]);
        }
    }

    /** Copia una imagen de database/seeders/assets al disco público y devuelve su ruta. */
    private function asset(string $file, string $folder): string
    {
        $path = "{$folder}/{$file}";

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, file_get_contents(__DIR__."/assets/{$file}"));
        }

        return $path;
    }
}
