import Swiper from 'swiper';
import { A11y, Autoplay, Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/a11y';
import '../assets/css/carousels.css';

document.querySelectorAll('[data-book-carousel]').forEach(root => {
  const viewport = root.querySelector('.swiper');
  const count = viewport.querySelectorAll('.swiper-slide').length;
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const previous = root.querySelector('[data-carousel-prev]');
  const next = root.querySelector('[data-carousel-next]');
  const pause = root.querySelector('[data-carousel-pause]');
  const slider = new Swiper(viewport, {
    modules: [A11y, Autoplay, Navigation],
    slidesPerView: 1.2,
    spaceBetween: 16,
    breakpointsBase: 'container',
    breakpoints: {480: {slidesPerView: 2}, 760: {slidesPerView: 3}, 1100: {slidesPerView: 4}},
    loop: count >= 6,
    rewind: count < 6,
    watchOverflow: true,
    speed: reducedMotion ? 0 : 350,
    navigation: {prevEl: previous, nextEl: next},
    a11y: {prevSlideMessage: previous.getAttribute('aria-label'), nextSlideMessage: next.getAttribute('aria-label')},
    autoplay: count > 1 && !reducedMotion ? {delay: 6000, pauseOnMouseEnter: true, disableOnInteraction: true} : false,
  });
  pause.hidden = !slider.autoplay.running;
  const updatePause = () => {
    pause.textContent = slider.autoplay.running ? pause.dataset.pause : pause.dataset.play;
    pause.setAttribute('aria-pressed', String(!slider.autoplay.running));
  };
  pause.addEventListener('click', () => {
    if (slider.autoplay.running) slider.autoplay.stop();
    else slider.autoplay.start();
    updatePause();
  });
  root.addEventListener('focusin', event => {
    if (event.target !== pause) { slider.autoplay.stop(); updatePause(); }
  });
  slider.on('autoplayStop', updatePause);
  slider.on('autoplayStart', updatePause);
  root.addEventListener('keydown', event => {
    if (event.key === 'ArrowRight') { event.preventDefault(); slider.slideNext(); }
    if (event.key === 'ArrowLeft') { event.preventDefault(); slider.slidePrev(); }
  });
  updatePause();
});
