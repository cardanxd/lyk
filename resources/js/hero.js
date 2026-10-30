// Carrusel de portadas: avance automático cada 6 s, se pausa al enfocar,
// al pasar el ratón, con el botón de pausa o si el usuario prefiere menos movimiento.
export function initCarousel(root) {
    const track = root.querySelector('[data-track]');
    const slides = [...track.children];
    if (slides.length < 2) return;

    const status = root.querySelector('[data-status]');
    const toggle = root.querySelector('[data-toggle]');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let index = 0;
    let paused = reduced;
    let hovering = false;
    let timer;

    const go = (i) => {
        index = (i + slides.length) % slides.length;
        track.scrollTo({ left: slides[index].offsetLeft, behavior: reduced ? 'auto' : 'smooth' });
        status.textContent = `Portada ${index + 1} de ${slides.length}`;
    };

    const setPaused = (value) => {
        paused = value;
        toggle.setAttribute('aria-label', paused ? 'Reproducir' : 'Pausar');
        toggle.querySelector('[data-icon-pause]').classList.toggle('hidden', paused);
        toggle.querySelector('[data-icon-play]').classList.toggle('hidden', !paused);
    };

    const schedule = () => {
        clearInterval(timer);
        timer = setInterval(() => {
            if (!paused && !hovering && !root.contains(document.activeElement)) go(index + 1);
        }, 6000);
    };

    root.querySelector('[data-prev]').addEventListener('click', () => go(index - 1));
    root.querySelector('[data-next]').addEventListener('click', () => go(index + 1));
    toggle.addEventListener('click', () => setPaused(!paused));
    root.addEventListener('mouseenter', () => (hovering = true));
    root.addEventListener('mouseleave', () => (hovering = false));
    track.addEventListener('scrollend', () => {
        index = Math.round(track.scrollLeft / track.clientWidth);
    });

    setPaused(paused);
    schedule();
}
