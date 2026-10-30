import 'trix';
import 'trix/dist/trix.css';
import '../css/admin-editor.css';

// Las imágenes de los posts se suben como portada; el editor no acepta archivos.
document.addEventListener('trix-file-accept', (event) => event.preventDefault());
