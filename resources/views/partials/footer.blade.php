<footer class="mt-16 bg-navy text-white">
    <div class="container-page grid gap-10 py-12 md:grid-cols-3">
        <div>
            <img src="{{ app(\App\Support\Settings::class)->imageUrl('logo_path', 'images/brand/logo.png') }}" alt="" class="mb-4 h-20 w-auto rounded-xl bg-white p-1" width="80" height="80">
            <p class="font-serif text-lg">{{ setting('tagline', 'Hacemos de tu retiro una nueva oportunidad') }}</p>
        </div>
        <div>
            <h2 class="mb-3 font-sans text-base font-bold tracking-widest text-gold uppercase">Navegación</h2>
            <ul class="space-y-2">
                <li><a href="{{ route('home') }}" class="hover:text-gold">Inicio</a></li>
                @if (Route::has('posts.index'))<li><a href="{{ route('posts.index') }}" class="hover:text-gold">Posts</a></li>@endif
                @if (Route::has('about'))<li><a href="{{ route('about') }}" class="hover:text-gold">Nosotros</a></li>@endif
                @if (Route::has('contact'))<li><a href="{{ route('contact') }}" class="hover:text-gold">Contacto</a></li>@endif
            </ul>
        </div>
        <div>
            <h2 class="mb-3 font-sans text-base font-bold tracking-widest text-gold uppercase">Contacto</h2>
            <ul class="space-y-2">
                @if ($phone = setting('phone'))<li><a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="hover:text-gold">Tel. {{ $phone }}</a></li>@endif
                @if ($email = setting('email'))<li><a href="mailto:{{ $email }}" class="break-all hover:text-gold">{{ $email }}</a></li>@endif
                @if ($address = setting('address'))<li class="text-white/85">{{ $address }}</li>@endif
                @if ($schedule = setting('schedule'))<li class="text-white/85">{{ $schedule }}</li>@endif
            </ul>
            @php($socials = array_filter([
                'Facebook' => setting('facebook_url'),
                'Instagram' => setting('instagram_url'),
                'TikTok' => setting('tiktok_url'),
                'YouTube' => setting('youtube_url'),
            ]))
            @if ($socials)
                <ul class="mt-4 flex flex-wrap gap-2">
                    @foreach ($socials as $network => $url)
                        <li><a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center rounded-full border border-white/30 px-4 hover:border-gold hover:text-gold">{{ $network }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    <div class="border-t border-white/15">
        <div class="container-page flex flex-col gap-2 py-5 text-sm text-white/80 sm:flex-row sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ setting('site_name', 'LyK Asesoría y Consultoría de Pensiones y AFORE') }}</p>
            @if (Route::has('privacy'))<a href="{{ route('privacy') }}" class="underline hover:text-gold">Aviso de privacidad</a>@endif
        </div>
    </div>
</footer>
