// Visor de fotos de la galería de un post: <dialog> con anterior/siguiente, flechas y Esc.
export function initGallery(root) {
    const dialog = root.querySelector('[data-gallery-dialog]');
    if (!dialog) return;

    const items = JSON.parse(dialog.querySelector('[data-gallery-items]').textContent);
    const image = dialog.querySelector('[data-gallery-image]');
    const caption = dialog.querySelector('[data-gallery-caption]');
    let index = 0;
    let opener = null;

    const show = (i) => {
        index = (i + items.length) % items.length;
        image.src = items[index].src;
        image.alt = items[index].alt;
        caption.textContent = items[index].caption || `Foto ${index + 1} de ${items.length}`;
    };

    root.querySelectorAll('[data-gallery-open]').forEach((button) => {
        button.addEventListener('click', () => {
            opener = button;
            show(Number(button.dataset.galleryOpen));
            dialog.showModal();
        });
    });

    dialog.querySelector('[data-gallery-prev]').addEventListener('click', () => show(index - 1));
    dialog.querySelector('[data-gallery-next]').addEventListener('click', () => show(index + 1));
    dialog.querySelector('[data-gallery-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') show(index - 1);
        if (event.key === 'ArrowRight') show(index + 1);
    });
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });
    dialog.addEventListener('close', () => opener?.focus());

    const single = items.length < 2;
    dialog.querySelector('[data-gallery-prev]').hidden = single;
    dialog.querySelector('[data-gallery-next]').hidden = single;
}
