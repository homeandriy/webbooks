import Swiper from 'swiper';
import { A11y, Navigation, Thumbs, Zoom } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/a11y';
import 'swiper/css/zoom';
import '../assets/css/carousels.css';
import '../assets/css/gallery.css';

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
function createViewer(root, main, thumbs = null, initialSlide = 0) {
  const previous = root.querySelector('[data-gallery-prev]');
  const next = root.querySelector('[data-gallery-next]');
  const zoom = root.querySelector('[data-gallery-zoom]');
  const slider = new Swiper(main, {
    modules: [A11y, Navigation, Thumbs, Zoom],
    initialSlide,
    slidesPerView: 1,
    speed: reducedMotion ? 0 : 300,
    watchOverflow: true,
    zoom: {maxRatio: 3, toggle: true, panOnMouseMove: true},
    navigation: {prevEl: previous, nextEl: next},
    thumbs: thumbs ? {swiper: thumbs} : undefined,
    a11y: {
      prevSlideMessage: previous?.getAttribute('aria-label') || '',
      nextSlideMessage: next?.getAttribute('aria-label') || '',
    },
  });
  zoom.addEventListener('click', () => slider.zoom.toggle());
  slider.on('zoomChange', (_swiper, scale) => zoom.setAttribute('aria-pressed', String(scale > 1)));
  root.addEventListener('keydown', event => {
    if (event.key === 'ArrowRight') { event.preventDefault(); event.stopPropagation(); slider.slideNext(); }
    if (event.key === 'ArrowLeft') { event.preventDefault(); event.stopPropagation(); slider.slidePrev(); }
  });
  return slider;
}

document.querySelectorAll('[data-book-gallery]').forEach(root => {
  const thumbsNode = root.querySelector('.book-gallery-thumbs');
  const thumbs = thumbsNode ? new Swiper(thumbsNode, {
    slidesPerView: 3,
    spaceBetween: 8,
    watchSlidesProgress: true,
    watchOverflow: true,
    speed: reducedMotion ? 0 : 300,
  }) : null;
  const viewer = createViewer(root, root.querySelector('.book-gallery-main'), thumbs);
  if (thumbsNode) {
    thumbsNode.addEventListener('click', event => {
      const link = event.target.closest('[data-gallery-index]');
      if (!link) return;
      event.preventDefault();
      viewer.slideTo(Number(link.dataset.galleryIndex));
    });
    const markThumb = () => {
      thumbsNode.querySelectorAll('[data-gallery-index]').forEach(link => {
        if (Number(link.dataset.galleryIndex) === viewer.activeIndex) link.setAttribute('aria-current', 'true');
        else link.removeAttribute('aria-current');
      });
    };
    viewer.on('slideChange', markThumb);
    markThumb();
  }

  const dialog = root.querySelector('dialog');
  let expandedViewer = null;
  root.querySelector('[data-gallery-expand]').addEventListener('click', () => {
    if (dialog.open) return;
    const content = dialog.querySelector('[data-gallery-dialog-content]');
    content.replaceChildren();
    const main = root.querySelector('.book-gallery-main').cloneNode(true);
    main.removeAttribute('style');
    main.className = 'swiper book-gallery-main';
    const wrapper = main.querySelector('.swiper-wrapper');
    wrapper.removeAttribute('style');
    wrapper.removeAttribute('id');
    main.querySelectorAll('.swiper-slide, .swiper-zoom-container, img').forEach(node => node.removeAttribute('style'));
    main.querySelectorAll('.swiper-slide').forEach(node => { node.className = 'swiper-slide'; });
    const controls = root.querySelector('.book-gallery-controls').cloneNode(true);
    controls.querySelector('[data-gallery-expand]').remove();
    controls.querySelector('[data-gallery-zoom]').setAttribute('aria-pressed', 'false');
    content.append(main, controls);
    dialog.showModal();
    expandedViewer = createViewer(content, main, null, viewer.activeIndex);
  });
  dialog.querySelector('[data-gallery-close]').addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', event => { if (event.target === dialog) dialog.close(); });
  dialog.addEventListener('close', () => {
    if (expandedViewer) {
      viewer.slideTo(expandedViewer.activeIndex, 0);
      expandedViewer.destroy(true, true);
      expandedViewer = null;
    }
    dialog.querySelector('[data-gallery-dialog-content]').replaceChildren();
  });
});
