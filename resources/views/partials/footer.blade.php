<footer class="mt-16 bg-navy text-white">
    <div class="container-page grid gap-10 py-12 md:grid-cols-3">
        <div>
            <img src="{{ asset('images/brand/logo.png') }}" alt="" class="mb-4 h-20 w-auto rounded-xl bg-white p-1" width="80" height="80">
            <p class="font-serif text-lg">Hacemos de tu retiro una nueva oportunidad</p>
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
        </div>
    </div>
    <div class="border-t border-white/15">
        <div class="container-page flex flex-col gap-2 py-5 text-sm text-white/80 sm:flex-row sm:justify-between">
            <p>&copy; {{ date('Y') }} LyK Asesoría y Consultoría de Pensiones y AFORE</p>
            @if (Route::has('privacy'))<a href="{{ route('privacy') }}" class="underline hover:text-gold">Aviso de privacidad</a>@endif
        </div>
    </div>
</footer>
