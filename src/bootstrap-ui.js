import Collapse from 'bootstrap/js/dist/collapse';
import Dropdown from 'bootstrap/js/dist/dropdown';
import Modal from 'bootstrap/js/dist/modal';
import Offcanvas from 'bootstrap/js/dist/offcanvas';
import Tab from 'bootstrap/js/dist/tab';
import Tooltip from 'bootstrap/js/dist/tooltip';

window.bootstrap = {Collapse, Dropdown, Modal, Offcanvas, Tab, Tooltip};

const overlaySelector = '#mobile-search-modal, #webbooks-sidebar, #webbooks-author-support-modal';

const syncOverlayState = () => {
    const overlayIsOpen = Array.from(document.querySelectorAll(overlaySelector))
        .some((element) => element.classList.contains('show'));

    document.body.classList.toggle('webbooks-overlay-open', overlayIsOpen);
};

const prepareOverlay = (event) => {
    if (event.target instanceof HTMLElement && event.target.matches(overlaySelector)) {
        document.body.classList.add('webbooks-overlay-open');
    }
};

document.addEventListener('show.bs.modal', prepareOverlay);
document.addEventListener('shown.bs.modal', syncOverlayState);
document.addEventListener('hidden.bs.modal', syncOverlayState);
document.addEventListener('show.bs.offcanvas', prepareOverlay);
document.addEventListener('shown.bs.offcanvas', syncOverlayState);
document.addEventListener('hidden.bs.offcanvas', syncOverlayState);

document.addEventListener('shown.bs.modal', (event) => {
    const modal = event.target;
    if (!(modal instanceof HTMLElement) || modal.id !== 'mobile-search-modal') {
        return;
    }

    const input = modal.querySelector('[data-webbooks-mobile-search-input]');
    if (!(input instanceof HTMLInputElement) || input.disabled) {
        return;
    }

    window.setTimeout(() => input.focus({preventScroll: true}), 0);
});

// Delegated data-bs controls also support markup inserted by AJAX.
window.WebBooksUI = {
  showModal(selector) {
    const element = document.querySelector(selector);
    if (element) Modal.getOrCreateInstance(element).show();
  },
  showCollapse(selector) {
    const element = document.querySelector(selector);
    if (element) Collapse.getOrCreateInstance(element, {toggle: false}).show();
  },
};
export {Collapse, Dropdown, Modal, Offcanvas, Tab, Tooltip};
