import { initCarousel } from './hero';
import { initGallery } from './gallery';

document.querySelectorAll('[data-carousel]').forEach(initCarousel);
document.querySelectorAll('[data-gallery]').forEach(initGallery);
