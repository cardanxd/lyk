<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name' => 'LyK Asesoría y Consultoría de Pensiones y AFORE',
            'tagline' => 'Hacemos de tu retiro una nueva oportunidad',
            'mission' => '[Reemplazar] Acompañar a cada persona en la planeación de su retiro con asesoría clara, honesta y personalizada, para que obtenga la mejor pensión posible.',
            'vision' => '[Reemplazar] Ser la consultoría de pensiones y AFORE de mayor confianza en México, reconocida por transformar el retiro de nuestros clientes en una nueva oportunidad.',
            'values' => "[Reemplazar]\nHonestidad\nCercanía\nCompromiso\nClaridad",
            'history' => '[Reemplazar] Cuéntale a tus visitantes cómo nació LyK y qué te motiva a ayudar a las personas con su retiro.',
            'schedule' => 'Lunes a viernes de 9:00 a 18:00 h',
            'seo_description' => 'Asesoría y consultoría de pensiones IMSS, Modalidad 40 y AFORE. Hacemos de tu retiro una nueva oportunidad.',
            'privacy_notice' => $this->privacyNotice(),
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        cache()->forget(Settings::CACHE_KEY);
    }

    private function privacyNotice(): string
    {
        return <<<'TXT'
[Reemplazar con el aviso revisado por un profesional]

LyK Asesoría y Consultoría de Pensiones y AFORE es responsable del tratamiento de los datos personales que nos proporcionas, conforme a la Ley Federal de Protección de Datos Personales en Posesión de los Particulares.

Datos que recabamos: nombre, teléfono, correo electrónico y la información que compartas en tu mensaje.

Finalidad: contactarte para brindarte la asesoría solicitada y dar seguimiento a tu caso. No compartimos tus datos con terceros sin tu consentimiento, salvo obligación legal.

Derechos ARCO: puedes solicitar el acceso, rectificación, cancelación u oposición al tratamiento de tus datos escribiendo a nuestro correo de contacto.

Cualquier cambio a este aviso se publicará en esta misma página.
TXT;
    }
}
